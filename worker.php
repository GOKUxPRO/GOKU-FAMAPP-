<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

while (true) {
    $processedCount = 0;

    try {
        // 1. Firebase thi badha active users lo
        $allUsers = $database->getReference('users')->getValue() ?: [];

        foreach ($allUsers as $userId => $user) {
            if (strtolower(trim($user['status'] ?? '')) !== 'active') continue;

            $email = $user['email'] ?? '';
            $pass = decrypt_pass($user['app_password'] ?? '');
            
            if (!$email || !$pass) continue;

            // 2. Gmail ma Login (Jùna code jevu j simple)
            $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email, $pass);
            
            if ($inbox) {
                // 3. Search: Jùna code jevu j simple (X-GM-RAW hataavi didhu che)
                $emails = imap_search($inbox, 'FROM "famapp"');
                
                if ($emails) {
                    rsort($emails); 
                    $recentEmails = array_slice($emails, 0, 15);

                    foreach ($recentEmails as $email_number) {
                        $header = imap_headerinfo($inbox, $email_number);
                        $message_id = md5($userId . ($header->message_id ?? time()));

                        // Duplicate check
                        if ($database->getReference('processed_emails/' . $message_id)->getValue()) continue;

                        // 4. Body Read: Jùna code jevu j simple
                        $body = imap_fetchbody($inbox, $email_number, 1.2) ?: imap_fetchbody($inbox, $email_number, 1);
                        $cleanBody = strip_tags(quoted_printable_decode($body));
                        $cleanBody = html_entity_decode($cleanBody, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $cleanBody = preg_replace('/\s+/', ' ', trim($cleanBody));

                        // 5. Amount Extract
                        if (preg_match('/₹\s*([\d,]+(?:\.\d{1,2})?)/', $cleanBody, $matches)) {
                            $amount = floatval(str_replace(',', '', $matches[1]));
                            
                            // UTR Extract (FamApp ni email ma UTR hovay che)
                            $utr = "";
                            if (preg_match('/UTR\s*[:\-]?\s*(\d{10,15})/i', $cleanBody, $utrMatch)) {
                                $utr = trim($utrMatch[1]);
                            }

                            // Sender Name Extract
                            $sender = "Unknown";
                            if (preg_match('/from\s+([A-Za-z][A-Za-z0-9\s\.\-]{1,45}?)(?=\s*(?:Transaction|Txn|Date|Updated|Balance|If|on\s|$))/iu', $cleanBody, $m)) {
                                $sender = trim($m[1]);
                            }

                            if ($amount > 0) {
                                // 6. Firebase ma save karo (Tamara nava structure pramane)
                                $database->getReference('payments/' . $userId)->push([
                                    'amount'         => $amount,
                                    'sender_name'    => $sender,
                                    'transaction_id' => 'AUTO_' . time(),
                                    'utr'            => $utr,
                                    'created_at'     => time()
                                ]);
                                
                                echo "✅ Saved for {$email}: ₹{$amount} | UTR: {$utr}\n";
                                $processedCount++;
                            }
                        }

                        // Processed mark karo
                        $database->getReference('processed_emails/' . $message_id)->set(time());
                    }
                }
                imap_close($inbox);
            } else {
                echo "❌ IMAP Login Failed for {$email}\n";
            }
        }
    } catch (\Throwable $e) {
        echo "💥 Error: " . $e->getMessage() . "\n";
    }

    echo "Cycle complete. Processed: {$processedCount}. Sleeping 25s...\n";
    sleep(25);
}
