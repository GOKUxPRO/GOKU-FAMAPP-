#!/bin/sh
# Worker ne background maa start karo
php /app/worker.php &

# Web server start karo
exec frankenphp run --config /app/Caddyfile
