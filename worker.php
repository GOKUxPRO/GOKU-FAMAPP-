<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

function get_email_body($inbox, $msgNo) {
    $structure = @imap_fetchstructure($inbox, $msgNo);
    if (!$structure) {
        return imap_body($inbox, $msgNo) ?: '';
    }

    if (empty($structure->parts)) {
        $body = imap_body($inbox, $msgNo);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);
        return $body ?: '';
    }

    $parts = $structure->parts;
    $html = '';
    $plain = '';

    foreach ($parts as $i => $part) {
        $partNo = $i + 1;
        $data = imap_fetchbody($inbox, $msgNo, (string)$partNo);

        if ($part->encoding == 3) $data = base64_decode($data);
        if ($part->encoding == 4) $data = quoted_printable_decode($data);

        $subtype = strtoupper($part->subtype ?? '');

        if ($subtype === 'HTML')      $html  = $data;
        elseif ($subtype === 'PLAIN') $plain = $data;

        if (!empty($part->parts)) {
            foreach ($part->parts as $j => $sub) {
                $subNo   = $partNo . '.' . ($j + 1);
                $subData = imap_fetchbody($inbox, $msgNo, $subNo);
                if ($sub->encoding == 3) $subData = base64_decode($subData);
                if ($sub->encoding == 4) $subData = quoted_printable_decode($subData);

                $subType = strtoupper($sub->subtype ?? '');
                if ($subType === 'HTML'  && !$html)  $html  = $subData;
                if ($subType === 'PLAIN' && !$plain) $plain = $subData;
            }
        }
    }

    return $html ?: $plain ?: imap_body($inbox, $msgNo) ?: '';
}

function search_famapp_emails($inbox) {
    // Strategy 1: Gmail native search (best — searches all categories)
    $result = @imap_search($inbox, 'X-GM-RAW "from:(famapp.in OR fampay.in OR famapp.com)"');
    if ($result) return $result;

    // Strategy 2: Simple partial match
    $result = @imap_search($inbox, 'FROM "famapp"');
    if ($result) return $result;

    $result = @imap_search($inbox, 'FROM "fampay"');
    if ($result) return $result;

    // Strategy 3: Proper OR syntax (right-associative)
    $result = @imap_search($inbox, 'OR FROM "no-reply@famapp.in" OR FROM "noreply@famapp.in" FROM "hello@famapp.in"');
    if ($result) return $result;

    // Strategy 4: Subject-based fallback
    $result = @imap_search($inbox, 'SUBJECT "famapp"');
    if ($result) return $result;

    $result = @imap_search($inbox, 'SUBJECT "fampay"');
    if ($result) return $result;

    return false;
}

while (true) {
    $processedCount = 0;

    try {
        // Fetch all users, filter in PHP (no Firebase index needed)
        $allUsers = $database->getReference('users')->getValue() ?: [];
        $activeUsers = [];

        foreach ($allUsers as $userId => $user) {
            $status = strtolower(trim($user['status'] ?? ''));
            if ($status === 'active') {
                $activeUsers[$userId] = $user;
            }
        }

        echo "👥 Total users: " . count($allUsers) . " | Active: " . count($activeUsers) . "\n";

        if (empty($activeUsers)) {
            echo "⚠️ No active users. Sleeping...\n";
            sleep(25);
            continue;
        }

        foreach ($activeUsers as $userId => $user) {
            $email = $user['email'] ?? '';
            if (!$email) {
                echo "❌ Missing email for user {$userId}\n";
                continue;
            }

            $pass = decrypt_pass($user['app_password'] ?? '');
            if (!$pass) {
                echo "❌ Decrypt failed for {$userId} ({$email})\n";
                continue;
            }

            // Search both INBOX and All Mail
            $folders = ['INBOX', '[Gmail]/All Mail'];
            $foundAny = false;

            foreach ($folders as $folder) {
                $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}{$folder}", $email, $pass);
                if (!$inbox) {
                    echo "❌ IMAP failed [{$folder}] {$email}: " . imap_last_error() . "\n";
                    continue;
                }

                $emails = search_famapp_emails($inbox);

                if (!$emails) {
                    echo "📭 [{$folder}] No FamApp emails for {$email}\n";
                    imap_close($inbox);
                    continue;
                }

                echo "📬 [{$folder}] Found " . count($emails) . " emails for {$email}\n";
                $foundAny = true;

                rsort($emails);
                $emails = array_slice($emails, 0, 15);

                foreach ($emails as $num) {
                    $header = @imap_headerinfo($inbox, $num);
                    if (!$header) {
                        echo "   ⚠️ headerinfo failed for msg {$num}\n";
                        continue;
                    }

                    $mid = trim($header->message_id ?? '', '<> ');
                    $uid = imap_uid($inbox, $num);

                    $key = $mid ? md5($userId . $mid) : md5($userId . 'uid_' . $folder . '_' . $uid);

                    if ($database->getReference('processed_emails/' . $key)->getValue()) {
                        continue;
                    }

                    $body = get_email_body($inbox, $num);
                    $clean = strip_tags($body);
                    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $clean = preg_replace('/\s+/', ' ', $clean);
                    $clean = trim($clean);

                    echo "   → Processing: " . substr($clean, 0, 120) . "...\n";

                    $isPayment = (
                        stripos($clean, 'you received') !== false ||
                        stripos($clean, 'successfully received') !== false ||
                        stripos($clean, 'you have successfully received') !== false ||
                        stripos($clean, 'money received') !== false ||
                        stripos($clean, 'payment received') !== false ||
                        stripos($clean, 'amount received') !== false ||
                        stripos($clean, 'credited') !== false ||
                        stripos($clean, 'received ₹') !== false ||
                        stripos($clean, 'received rs') !== false ||
                        preg_match('/received\s+(₹|rs\.?)\s*[\d,]+/i', $clean)
                    );

                    if ($isPayment) {
                        $amount = 0;
                        $sender = "Unknown";
                        $txn    = "";

                        if (preg_match('/(?:₹|Rs\.?|INR)\s*([\d,]+(?:\.\d{1,2})?)/iu', $clean, $m)) {
                            $amount = floatval(str_replace(',', '', $m[1]));
                        }

                        if (preg_match('/from\s+([A-Za-z][A-Za-z0-9\s\.\-]{1,45}?)(?=\s*(?:Transaction|Txn|Date|Updated|Balance|If|on\s|$))/iu', $clean, $m)) {
                            $sender = trim($m[1]);
                        } elseif (preg_match('/(?:from|by)\s+([A-Za-z][A-Za-z0-9\s\.\-]{2,40})/iu', $clean, $m)) {
                            $sender = trim($m[1]);
                        }

                        if (preg_match('/(?:Transaction\s*ID|Txn\s*ID|UTR|Ref\.?\s*No\.?|Reference)\s*[:\-]?\s*([A-Z0-9]{8,})/i', $clean, $m)) {
                            $txn = trim($m[1]);
                        }

                        if ($amount > 0) {
                            $database->getReference('payments/' . $userId)->push([
                                'amount'          => $amount,
                                'sender_name'    => $sender,
                                'transaction_id' => $txn,
                                'created_at'     => time(),
                            ]);
                            echo "   ✅ Saved: ₹{$amount} from {$sender} | TXN: {$txn}\n";
                            $processedCount++;
                        } else {
                            echo "   ⚠️ Payment keywords found but amount not extracted.\n";
                            echo "   Snippet: " . substr($clean, 0, 300) . "\n";
                        }
                    }

                    $database->getReference('processed_emails/' . $key)->set(time());
                }

                imap_close($inbox);
            }

            if (!$foundAny) {
                echo "⚠️ No FamApp emails anywhere for {$email}\n";
            }

            sleep(1);
        }
    } catch (\Throwable $e) {
        echo "💥 Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

    echo "✅ Cycle complete. Processed: {$processedCount}. Sleeping 25s...\n";
    sleep(25);
}
