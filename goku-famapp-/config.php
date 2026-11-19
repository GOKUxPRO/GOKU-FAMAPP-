<?php
date_default_timezone_set('Asia/Kolkata');

define('FIREBASE_DB_URL', getenv('FIREBASE_DB_URL') ?: '');
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'GokuPaySuperSecretKey2026ChangeMe');
define('CRON_SECRET', getenv('CRON_SECRET') ?: 'goku_cron_secret_123');
define('API_RATE_LIMIT_PER_MINUTE', 60);
