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

            $emails = imap_search($inbox, 'FROM "famapp"');
            if ($emails) {
                rsort($emails);
                foreach (array_slice($emails, 0, 5) as $num) {
                    $header = imap_headerinfo($inbox, $num);
                    $mid = $header->message_id ?? '';
                    $key = md5($userId . $mid);
                    if ($mid && $database->getReference('processed_emails/' . $key)->getValue()) continue;

                    $body = imap_fetchbody($inbox, $num, '1.2') ?: imap_fetchbody($inbox, $num, '1');
                    $clean = strip_tags(quoted_printable_decode($body));
                    $clean = preg_replace('/\s+/', ' ', $clean);

                    if (strpos($clean, 'You received') !== false || strpos($clean, 'successfully received') !== false) {
                        $amount = 0; $sender = "Unknown"; $utr = ""; $txn = "";
                        if (preg_match('/₹\s*([\d,]+(?:\.\d{1,2})?)/u', $clean, $m)) $amount = floatval(str_replace(',', '', $m[1]));
                        if (preg_match('/from\s+([A-Za-z\s\.]+?)(?=\s*Transaction|\s*Date|\s*UTR|\s*$)/i', $clean, $m)) $sender = trim($m[1]);
                        if (preg_match('/UTR\s*:\s*([0-9]{12})/i', $clean, $m)) $utr = $m[1];
                        if (preg_match('/Transaction ID\s*:\s*([A-Z0-9]+)/i', $clean, $m)) $txn = $m[1];

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
