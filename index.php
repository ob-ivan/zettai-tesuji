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
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver'    => 'pdo_mysql',
        'host'      => $config->db->host,
        'dbname'    => $config->db->dbname,
        'user'      => $config->db->user,
        'password'  => $config->db->password,
        'charset'   => 'utf8',
    ],
]);
$app['model'] = $app->share(function () use ($app) {
    return new Zettai\Model($app['db']);
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
        ['^/admin/', 'ROLE_ADMIN'],
    ]
]);
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/template',
]);
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new Twig_SimpleFunction('ceil',  function ($float) { return ceil  ($float); }));
    $twig->addFunction(new Twig_SimpleFunction('floor', function ($float) { return floor ($float); }));
    return $twig;
}));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Задаём рутинг и контроллеры.

// Заглушка для главной страницы.
$app->get('/', function () use ($app) {
    return $app->render('dummy.twig');
});

// Вход в админку.
$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

// Главная страница админки.
$app->get('/admin/{page}', function ($page) use ($app) {
    $mondaiCount = $app['model']->getMondaiCount();
    $perPage = 20;
    if (($page - 1) * $perPage > $mondaiCount) {
        return $app->redirect($app['url_generator']->generate('admin_page', ['page' => 1]));
    }
    $mondaiList = $app['model']->getMondaiList(($page - 1) * $perPage, $perPage);
    
    return $app->render('admin/main.twig', [
        'mondaiList'  => $mondaiList,
        'mondaiCount' => $mondaiCount,
        'curPage'     => $page,
        'perPage'     => $perPage,
    ]);
})
->assert('page', '\\d*')
->convert('page', function ($page) {
    $page = intval ($page);
    if ($page < 1) {
        $page = 1;
    }
    return $page;
})
->bind('admin_page');

// Страница просмотра задачи в админке.
$app->get('/admin/mondai/view/{mondai_id}', function (Request $request, $mondai_id) use ($app) {
    $mondai = $app['model']->getMondai($mondai_id);
    $page = $request->get('page');
    return $app->render('admin/mondai/view.twig', [
        'mondai' => $mondai,
        'page' => $page,
    ]);
})
->assert('mondai_id', '\\d+')
->convert('mondai_id', function ($mondai_id) {
    $mondai_id = intval ($mondai_id);
    if ($mondai_id < 1) {
        throw new Exception('Mondai id must be positive integer');
    }
    return $mondai_id;
})
->bind('admin_mondai_view');

// На дев-хосте добавляем генератор паролей.
if ($config->debug) {
    $app->get('/password/{password}/{salt}', function ($password, $salt) use ($app) {
        return $app['security.encoder.digest']->encodePassword($password, $salt);
    })
    ->value('salt', '');
}

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
