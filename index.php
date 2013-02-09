<?php
$time = microtime(true);

// Зависимости.

require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;

// Загружаем конфиги.

$config = new Zettai\Config(__DIR__);

// Инициализируем приложение.

$app = new Zettai\Application();

if ($config->debug) {
    $app['debug'] = true;
}
$app['config'] = $app->share(function () use ($config) {
    return $config;
});
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'admin' => [
            'pattern' => '^/admin/',
            'form' => ['login_path' => '/login', 'check_path' => '/admin/login_check'],
            'logout' => ['logout_path' => '/admin/logout'],
            'users' => $app->share(function() use ($app) {
                return new Zettai\UserProvider($app['config']);
            }),
        ],
    ],
    'security.access_rules' => [
        array('^/admin/', 'ROLE_ADMIN'),
    ]
]);
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/template',
]);
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Задаём рутинг и контроллеры.

// Заглушка для главной страницы.
$app->get('/', function () use ($app) {
    return $app->render('dummy.twig');
});

// Вход в админку.
$app->get('/admin/', function () use ($app) {
    return $app->render('admin/main.twig');
});
$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});
// На дев-хосте добавляем генератор паролей.
$app->get('/password/{password}/{salt}', function ($password, $salt) use ($app) {
    return $app['security.encoder.digest']->encodePassword($password, $salt);
})
->value('salt', '');

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
