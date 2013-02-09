<?php
$time = microtime(true);
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем конфиги.

$config = new Zettai\Config(__DIR__);

// Инициализируем приложение.

$app = new Zettai\Application();

if ($config->debug) {
    $app['debug'] = true;
}
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/template',
]);
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'admin' => [
            'pattern' => '^/admin/',
            'form' => [
                'login_path'  => '/login',
                'check_path'  => 'login_check',
            ],
            'logout' => ['logout_path' => '/logout'],
            'users' => $app->share(function() use ($app, $config) {
                return new Zettai\UserProvider($config);
            }),
        ],
    ],
    'security.access_rules' => [
        array('^/admin/', 'ROLE_ADMIN'),
    ]
]);

// Задаём рутинг и контроллеры.

// Заглушка для главной страницы.
$app->get('/', function () use ($app) {
    return $app->render('dummy.twig');
});

// Вход в админку.
$app->get('/admin/', function () use ($app) {
    return $app->render('admin/main.twig', [
    ]);
});
$app->get('/login', function () use ($app) {
    return $app->render('login.twig', [
    ]);
});
$app->get('/admin/check_path', function () use ($app) {
    // TODO
})
->bind('login_check');

// DEBUG
print $app['url_generator']->generate('login_check'); die;

// На дев-хосте добавляем генератор паролей.
$app->get('/password/{password}/{salt}', function ($password, $salt) use ($app, $config) {
    return $app['security.encoder.digest']->encodePassword($password, $salt);
})
->value('salt', '');

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
