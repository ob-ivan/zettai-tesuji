<?php
define('LOG_DIR', 'log');
define('LOG_PATH', LOG_DIR . '/githook.log');

try {
    $payload = json_decode($_POST['payload']);
} catch (Exception $e) {
    exit(0);
}

// log the request
if (! is_dir (LOG_DIR)) {
    mkdir (LOG_DIR, 0777, true);
}
file_put_contents(LOG_PATH, '[' . date('Y-m-d H:i:s') . '] payload = ' . print_r($payload, true), FILE_APPEND);

// only execute if pushed to master
if ($payload->ref === 'refs/heads/master')
{
    // run deployment script
    exec('./deploy.sh');
}
