<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

while (true) {
    $processedCount = 0;
    try {
        $users = $database->getReference('users')
            ->orderByChild('status')
            ->equalTo('active')
            ->getValue();

        foreach (($users ?: []) as $userId => $user) {
            $pass = decrypt_pass($user['app_password']);
            if (!$pass) continue;

            // Gmail IMAP connect
            $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $user['email'], $pass);
            if (!$inbox) {
                echo "IMAP failed for {$user['email']}: " . imap_last_error() . "\n";
                continue;
            }

            // FamApp emails search
            $emails = imap_search($inbox, 'FROM "no-reply@famapp.in"');
            if ($emails) {
                rsort($emails);
                foreach (array_slice($emails, 0, 10) as $num) {
                    $header   = imap_headerinfo($inbox, $num);
                    $mid      = $header->message_id ?? '';
                    $key      = md5($userId . $mid);

                    // Already processed? skip
                    if ($mid && $database->getReference('processed_emails/' . $key)->getValue()) {
                        continue;
                    }

                    // Email body fetch — try HTML part first, then plain
                    $body = imap_fetchbody($inbox, $num, '1.2');
                    if (!$body) $body = imap_fetchbody($inbox, $num, '1');
                    if (!$body) $body = imap_fetchbody($inbox, $num, '');

                    // Decode
                    $encoding = $header->encoding ?? 0;
                    if ($encoding == 4) {
                        $body = quoted_printable_decode($body);
                    } elseif ($encoding == 3) {
                        $body = base64_decode($body);
                    }

                    $clean = strip_tags($body);
                    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $clean = preg_replace('/\s+/', ' ', $clean);
                    $clean = trim($clean);

                    echo "Processing email for {$user['email']}: " . substr($clean, 0, 200) . "\n";

                    // Check if it's a payment received email
                    if (
                        stripos($clean, 'You received') !== false ||
                        stripos($clean, 'successfully received') !== false ||
                        stripos($clean, 'You have successfully received') !== false
                    ) {
                        $amount = 0;
                        $sender = "Unknown";
                        $txn    = "";

                        // Amount: ₹2.0 or ₹1,000.00
                        if (preg_match('/₹\s*([\d,]+(?:\.\d{1,2})?)/u', $clean, $m)) {
                            $amount = floatval(str_replace(',', '', $m[1]));
                        }

                        // Sender: "from Jyotikaben"
                        if (preg_match('/from\s+([A-Za-z][A-Za-z\s\.]{1,40}?)(?=\s*Transaction|\s*Date|\s*Updated|\s*Balance|\s*If\s)/i', $clean, $m)) {
                            $sender = trim($m[1]);
                        }

                        // Transaction ID: FMPIB7024950191
                        if (preg_match('/Transaction\s*ID\s*[:\-]?\s*([A-Z0-9]{6,})/i', $clean, $m)) {
                            $txn = trim($m[1]);
                        }

                        if ($amount > 0) {
                            $database->getReference('payments/' . $userId)->push([
                                'amount'         => $amount,
                                'sender_name'    => $sender,
                                'transaction_id' => $txn,
                                'created_at'     => time(),
                            ]);
                            echo "✅ Saved: ₹{$amount} from {$sender} | TXN: {$txn}\n";
                            $processedCount++;
                        } else {
                            echo "⚠️ Amount not found in email body.\n";
                        }
                    }

                    // Mark as processed
                    if ($mid) {
                        $database->getReference('processed_emails/' . $key)->set(time());
                    }
                }
            } else {
                echo "No FamApp emails found for {$user['email']}.\n";
            }

            imap_close($inbox);
            sleep(1);
        }
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "Cycle complete. Processed: {$processedCount}. Sleeping 30s...\n";
    sleep(30);
}
