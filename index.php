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
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/template',
]);

// Задаём рутинг и контроллеры.

$app->get('/', function () use ($app, $config) {
    return $app['twig']->render('main.twig', [
        'debug' => $config->debug,
    ]);
});

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
