<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

function get_email_body($inbox, $msgNo) {
    $structure = @imap_fetchstructure($inbox, $msgNo);
    if (!$structure) {
        return imap_body($inbox, $msgNo) ?: '';
    }

    $body = '';

    // Simple single-part
    if (empty($structure->parts)) {
        $body = imap_body($inbox, $msgNo);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);
        return $body ?: '';
    }

    // Multipart - prefer HTML then plain
    $parts = $structure->parts;
    $html = '';
    $plain = '';

    foreach ($parts as $i => $part) {
        $partNo = $i + 1;
        $data = imap_fetchbody($inbox, $msgNo, (string)$partNo);

        if ($part->encoding == 3) $data = base64_decode($data);
        if ($part->encoding == 4) $data = quoted_printable_decode($data);

        $subtype = strtoupper($part->subtype ?? '');

        if ($subtype === 'HTML') {
            $html = $data;
        } elseif ($subtype === 'PLAIN') {
            $plain = $data;
        }

        // Nested multipart
        if (!empty($part->parts)) {
            foreach ($part->parts as $j => $sub) {
                $subNo = $partNo . '.' . ($j + 1);
                $subData = imap_fetchbody($inbox, $msgNo, $subNo);
                if ($sub->encoding == 3) $subData = base64_decode($subData);
                if ($sub->encoding == 4) $subData = quoted_printable_decode($subData);

                $subType = strtoupper($sub->subtype ?? '');
                if ($subType === 'HTML' && !$html) $html = $subData;
                if ($subType === 'PLAIN' && !$plain) $plain = $subData;
            }
        }
    }

    return $html ?: $plain ?: imap_body($inbox, $msgNo) ?: '';
}

while (true) {
    $processedCount = 0;

    try {
        $users = $database->getReference('users')
            ->orderByChild('status')
            ->equalTo('active')
            ->getValue();

        foreach (($users ?: []) as $userId => $user) {
            $pass = decrypt_pass($user['app_password'] ?? '');
            if (!$pass) {
                echo "Decrypt failed for user {$userId}\n";
                continue;
            }

            $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $user['email'], $pass);
            if (!$inbox) {
                echo "IMAP failed for {$user['email']}: " . imap_last_error() . "\n";
                continue;
            }

            // Multiple possible senders
            $search = 'FROM "no-reply@famapp.in" OR FROM "noreply@famapp.in" OR FROM "no-reply@fampay.in" OR FROM "noreply@fampay.in" OR FROM "hello@famapp.in"';
            $emails = imap_search($inbox, $search);

            if (!$emails) {
                echo "No FamApp emails for {$user['email']}\n";
                imap_close($inbox);
                sleep(1);
                continue;
            }

            rsort($emails); // newest first
            $emails = array_slice($emails, 0, 15); // last 15

            foreach ($emails as $num) {
                $header = imap_headerinfo($inbox, $num);
                $mid    = $header->message_id ?? '';
                $uid    = imap_uid($inbox, $num);

                // Unique key: prefer message_id, fallback to uid
                $key = $mid ? md5($userId . $mid) : md5($userId . 'uid_' . $uid);

                if ($database->getReference('processed_emails/' . $key)->getValue()) {
                    continue;
                }

                $body = get_email_body($inbox, $num);
                $clean = strip_tags($body);
                $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $clean = preg_replace('/\s+/', ' ', $clean);
                $clean = trim($clean);

                echo "Processing [{$user['email']}] → " . substr($clean, 0, 180) . "...\n";

                // More flexible payment detection
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

                    // Amount: ₹2.0 / ₹1,000.00 / Rs. 50
                    if (preg_match('/(?:₹|Rs\.?|INR)\s*([\d,]+(?:\.\d{1,2})?)/iu', $clean, $m)) {
                        $amount = floatval(str_replace(',', '', $m[1]));
                    }

                    // Sender name
                    if (preg_match('/from\s+([A-Za-z][A-Za-z0-9\s\.\-]{1,45}?)(?=\s*(?:Transaction|Txn|Date|Updated|Balance|If|on\s|$))/iu', $clean, $m)) {
                        $sender = trim($m[1]);
                    } elseif (preg_match('/(?:from|by)\s+([A-Za-z][A-Za-z0-9\s\.\-]{2,40})/iu', $clean, $m)) {
                        $sender = trim($m[1]);
                    }

                    // Transaction / UTR / Ref ID
                    if (preg_match('/(?:Transaction\s*ID|Txn\s*ID|UTR|Ref\.?\s*No\.?|Reference)\s*[:\-]?\s*([A-Z0-9]{8,})/i', $clean, $m)) {
                        $txn = trim($m[1]);
                    }

                    if ($amount > 0) {
                        $database->getReference('payments/' . $userId)->push([
                            'amount'         => $amount,
                            'sender_name'   => $sender,
                            'transaction_id'=> $txn,
                            'created_at'    => time(),
                        ]);
                        echo "✅ Saved: ₹{$amount} from {$sender} | TXN: {$txn}\n";
                        $processedCount++;
                    } else {
                        echo "⚠️ Payment keywords found but amount not extracted.\n";
                        echo "   Clean body snippet: " . substr($clean, 0, 300) . "\n";
                    }
                }

                // Always mark as processed so we don't keep re-reading same email
                $database->getReference('processed_emails/' . $key)->set(time());
            }

            imap_close($inbox);
            sleep(1);
        }
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "Cycle complete. Processed: {$processedCount}. Sleeping 25s...\n";
    sleep(25);
}
