<?php
$time = microtime(true);
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

// $app['debug'] = true; // раскомментируй на деве, а ещё лучше -- внеси в конфиг хоста.

$app->get('/', function () {
    return 'hello world';
});

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
