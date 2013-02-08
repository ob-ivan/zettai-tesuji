<?php
$time = microtime(true);
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем конфиги.

$config = new Zettai\Config(__DIR__);

// Инициализируем приложение.

$app = new Silex\Application();

if ($config->debug) {
    $app['debug'] = true;
}

// Задаём рутинг и контроллеры.

$app->get('/', function () use ($config) {
    $output = '';
    if ($config->debug) {
        $output .= '[debug mode] ';
    }
    $output .= 'hello world';
    return $output;
});

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
