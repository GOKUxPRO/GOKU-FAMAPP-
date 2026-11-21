<?php
set_time_limit(0);
require __DIR__ . '/db.php';

echo "🚀 Worker Started...\n";

function get_email_body($inbox, $msgNo) {
    $structure = @imap_fetchstructure($inbox, $msgNo);
    if (!$structure) return imap_body($inbox, $msgNo) ?: '';
    if (empty($structure->parts)) {
        $body = imap_body($inbox, $msgNo);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);
        return $body ?: '';
    }
    $html = ''; $plain = '';
    foreach ($structure->parts as $i => $part) {
        $data = imap_fetchbody($inbox, $msgNo, (string)($i + 1));
        if ($part->encoding == 3) $data = base64_decode($data);
        if ($part->encoding == 4) $data = quoted_printable_decode($data);
        $subtype = strtoupper($part->subtype ?? '');
        if ($subtype === 'HTML') $html = $data;
        elseif ($subtype === 'PLAIN') $plain = $data;
    }
    return $html ?: $plain ?: imap_body($inbox, $msgNo) ?: '';
}

while (true) {
    $processedCount = 0;
    try {
        $allUsers = $database->getReference('users')->getValue() ?: [];
        foreach ($allUsers as $userId => $user) {
            if (strtolower(trim($user['status'] ?? '')) !== 'active') continue;

            $email = $user['email'] ?? '';
            $pass = decrypt_pass($user['app_password'] ?? '');
            if (!$email || !$pass) continue;

            $folders = ['INBOX', '[Gmail]/All Mail'];
            foreach ($folders as $folder) {
                $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}{$folder}", $email, $pass);
                if (!$inbox) continue;

                $emails = @imap_search($inbox, 'X-GM-RAW "from:(famapp.in OR fampay.in OR famapp.com)"');
                if (!$emails) { imap_close($inbox); continue; }

                echo "📬 Found " . count($emails) . " emails for {$email}\n";
                rsort($emails);
                $emails = array_slice($emails, 0, 15);

                foreach ($emails as $num) {
                    $header = @imap_headerinfo($inbox, $num);
                    if (!$header) continue;

                    $mid = trim($header->message_id ?? '', '<> ');
                    $key = $mid ? md5($userId . $mid) : md5($userId . 'uid_' . imap_uid($inbox, $num));

                    if ($database->getReference('processed_emails/' . $key)->getValue()) continue;

                    $body = get_email_body($inbox, $num);
                    $clean = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $clean = preg_replace('/\s+/', ' ', trim($clean));

                    $isPayment = (stripos($clean, 'you received') !== false || stripos($clean, 'credited') !== false || preg_match('/received\s+(₹|rs\.?)\s*[\d,]+/i', $clean));

                    if ($isPayment) {
                        $amount = 0; $sender = "Unknown"; $txn_id = ""; $utr = "";

                        if (preg_match('/(?:₹|Rs\.?|INR)\s*([\d,]+(?:\.\d{1,2})?)/iu', $clean, $m)) $amount = floatval(str_replace(',', '', $m[1]));
                        if (preg_match('/from\s+([A-Za-z][A-Za-z0-9\s\.\-]{1,45}?)(?=\s*(?:Transaction|Txn|Date|Updated|Balance|If|on\s|$))/iu', $clean, $m)) $sender = trim($m[1]);
                        if (preg_match('/(FMPIB[A-Z0-9]{8,15})/i', $clean, $m)) $txn_id = trim($m[1]);
                        if (preg_match('/UTR\s*[:\-]?\s*(\d{10,15})/i', $clean, $m)) $utr = trim($m[1]);

                        if ($amount > 0) {
                            $database->getReference('payments/' . $userId)->push([
                                'amount' => $amount, 'sender_name' => $sender, 'transaction_id' => $txn_id, 'utr' => $utr, 'created_at' => time()
                            ]);
                            echo "   ✅ Saved: ₹{$amount} | UTR: {$utr}\n";
                            $processedCount++;
                        }
                    }
                    $database->getReference('processed_emails/' . $key)->set(time());
                }
                imap_close($inbox);
            }
        }
    } catch (\Throwable $e) { echo "💥 Error: " . $e->getMessage() . "\n"; }
    sleep(25);
}
