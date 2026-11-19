<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

while (true) {
    $processedCount = 0;
    try {
        $users = $database->getReference('users')->orderByChild('status')->equalTo('active')->getValue();
        foreach (($users ?: []) as $userId => $user) {
            $pass = decrypt_pass($user['app_password']);
            if (!$pass) continue;

            $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $user['email'], $pass);
            if (!$inbox) continue;

            $emails = imap_search($inbox, 'FROM "no-reply@famapp.in"');
            if ($emails) {
                rsort($emails);
                foreach (array_slice($emails, 0, 5) as $num) {
                    $header = imap_headerinfo($inbox, $num);
                    $mid = $header->message_id ?? '';
                    $key = md5($userId . $mid);
                    if ($mid && $database->getReference('processed_emails/' . $key)->getValue()) continue;

                    // Try multipart first, then plain
                    $body = imap_fetchbody($inbox, $num, '1.2');
                    if (!$body) $body = imap_fetchbody($inbox, $num, '1');
                    if (!$body) $body = imap_fetchbody($inbox, $num, '');
                    $clean = strip_tags(quoted_printable_decode($body));
                    $clean = preg_replace('/\s+/', ' ', $clean);

                    echo "Debug body: " . substr($clean, 0, 300) . "\n";

                    if (strpos($clean, 'You received') !== false || strpos($clean, 'successfully received') !== false) {
                        $amount = 0; $sender = "Unknown"; $utr = ""; $txn = "";
                        // Amount: ₹2.0 or ₹1,000.00
                        if (preg_match('/₹\s*([\d,]+(?:\.\d{1,2})?)/u', $clean, $m)) $amount = floatval(str_replace(',', '', $m[1]));
                        // Sender name: "from Jyotikaben"
                        if (preg_match('/from\s+([A-Za-z\s\.]+?)(?=\s*Transaction|\s*Date|\s*Updated|\s*$)/i', $clean, $m)) $sender = trim($m[1]);
                        // Transaction ID: FMPIB7024950191
                        if (preg_match('/Transaction ID\s*[:\-]?\s*([A-Z0-9]{8,})/i', $clean, $m)) $txn = $m[1];

                        if ($amount > 0) {
                            $database->getReference('payments/' . $userId)->push([
                                'amount' => $amount, 'sender_name' => $sender,
                                'utr' => $utr, 'transaction_id' => $txn, 'created_at' => time()
                            ]);
                            $processedCount++;
                        }
                    }
                    if ($mid) $database->getReference('processed_emails/' . $key)->set(time());
                }
            }
            imap_close($inbox);
            sleep(1);
        }
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "Cycle complete. Processed: $processedCount. Sleeping 30s...\n";
    sleep(30);
}
