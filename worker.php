<?php
set_time_limit(0);
require __DIR__ . '/db.php'; // Aa tamari Firebase SDK vali file che

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
    $result = @imap_search($inbox, 'X-GM-RAW "from:(famapp.in OR fampay.in OR famapp.com)"');
    if ($result) return $result;

    $result = @imap_search($inbox, 'FROM "famapp"');
    if ($result) return $result;

    $result = @imap_search($inbox, 'FROM "fampay"');
    if ($result) return $result;

    return false;
}

while (true) {
    $processedCount = 0;

    try {
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
            if (!$email) continue;

            $pass = decrypt_pass($user['app_password'] ?? '');
            if (!$pass) {
                echo "❌ Decrypt failed for {$userId} ({$email})\n";
                continue;
            }

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
                    imap_close($inbox);
                    continue;
                }

                echo "📬 [{$folder}] Found " . count($emails) . " emails for {$email}\n";
                $foundAny = true;

                rsort($emails);
                $emails = array_slice($emails, 0, 15);

                foreach ($emails as $num) {
                    $header = @imap_headerinfo($inbox, $num);
                    if (!$header) continue;

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

                    $isPayment = (
                        stripos($clean, 'you received') !== false ||
                        stripos($clean, 'successfully received') !== false ||
                        stripos($clean, 'money received') !== false ||
                        preg_match('/received\s+(₹|rs\.?)\s*[\d,]+/i', $clean)
                    );

                    if ($isPayment) {
                        $amount = 0;
                        $sender = "Unknown";
                        $txn_id = ""; // FMPIB...
                        $utr    = ""; // 12 digit UTR

                        // 1. Amount
                        if (preg_match('/(?:₹|Rs\.?|INR)\s*([\d,]+(?:\.\d{1,2})?)/iu', $clean, $m)) {
                            $amount = floatval(str_replace(',', '', $m[1]));
                        }

                        // 2. Sender
                        if (preg_match('/from\s+([A-Za-z][A-Za-z0-9\s\.\-]{1,45}?)(?=\s*(?:Transaction|Txn|Date|Updated|Balance|If|on\s|$))/iu', $clean, $m)) {
                            $sender = trim($m[1]);
                        }

                        // 3. Transaction ID (FMPIB...)
                        if (preg_match('/(FMPIB[A-Z0-9]{8,15})/i', $clean, $m)) {
                            $txn_id = trim($m[1]); 
                        }

                        // 4. UTR (10 to 15 digits)
                        if (preg_match('/UTR\s*[:\-]?\s*(\d{10,15})/i', $clean, $m)) {
                            $utr = trim($m[1]); 
                        }

                        if ($amount > 0) {
                            $database->getReference('payments/' . $userId)->push([
                                'amount'         => $amount,
                                'sender_name'    => $sender,
                                'transaction_id' => $txn_id,
                                'utr'            => $utr,
                                'created_at'     => time(),
                            ]);
                            echo "   ✅ Saved: ₹{$amount} | TXN: {$txn_id} | UTR: {$utr}\n";
                            $processedCount++;
                        }
                    }

                    $database->getReference('processed_emails/' . $key)->set(time());
                }

                imap_close($inbox);
            }
            sleep(1);
        }
    } catch (\Throwable $e) {
        echo "💥 Error: " . $e->getMessage() . "\n";
    }

    echo "✅ Cycle complete. Processed: {$processedCount}. Sleeping 25s...\n";
    sleep(25);
}
