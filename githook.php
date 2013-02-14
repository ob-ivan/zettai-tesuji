<?php

define('LOG_DIR', 'log');
define('LOG_PATH', LOG_DIR . '/githook.log');

function writeLog ($message)
{
    if (! is_dir (LOG_DIR)) {
        mkdir (LOG_DIR, 0777, true);
    }
    file_put_contents(LOG_PATH, '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n", FILE_APPEND);
}

set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

try {
    $payload = json_decode($_POST['payload']);
} catch (Exception $e) {
    exit(0);
}

// log the request
writeLog('Payload = ' . print_r($payload, true));

// only execute if pushed to master
if ($payload->ref === 'refs/heads/master')
{
    writeLog('Ref is ok, running exec(deploy.sh)');
    
    // run deployment script
    try {
        $output = shell_exec('./deploy.sh 2>&1 >>' . LOG_PATH . ' &');
    } catch (Exception $e) {
        writeLog('Caught exception = ' . print_r($e, true));
    }
}
writeLog('Done.');