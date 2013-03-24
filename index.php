<?php
$time = microtime(true);

// Пути.
define ('DOCUMENT_ROOT', __DIR__);
define ('AUTOLOAD_PATH',    DOCUMENT_ROOT . '/vendor/autoload.php');
define ('DEPLOY_LOCK_PATH', DOCUMENT_ROOT . '/deploy.lock');
define ('TEMPLATE_DIR',     DOCUMENT_ROOT . '/template');

// Зависимости.

require_once AUTOLOAD_PATH;
use Symfony\Component\HttpFoundation\Request;

// Инициализируем приложение.

$app = new Zettai\Application(new Zettai\Config(DOCUMENT_ROOT));

$app['csrf'] = $app->share(function () use ($app) {
    return new Zettai\CsrfHandler($app['session']);
});
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'admin' => [
            'pattern' => '^/admin(/|$)',
            'form' => ['login_path' => '/login', 'check_path' => '/admin/login_check'],
            'logout' => ['logout_path' => '/admin/logout'],
            'users' => $app->share(function() use ($app) {
                return new Zettai\UserProvider($app['config']->security);
            }),
        ],
    ],
    'security.access_rules' => [
        ['^/admin/', 'ROLE_ADMIN'],
    ]
]);
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Zettai\Provider\TwigServiceProvider(), [
    'twig.path' => TEMPLATE_DIR,
]);
$app->register(new Zettai\Provider\TypeServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// TODO: Научиться обращаться с валидатором.
// $app->register(new Silex\Provider\ValidatorServiceProvider());

// Если стоит режим заглушки, то выводим её и больше ничего не делаем.
$app->before(function (Request $request) use ($app) {
    if (file_exists(DEPLOY_LOCK_PATH)) {
        if ($request->getMethod() === 'GET') {
            return $app->render('dummy.twig');
        }
        // TODO: Обработать POST-запросы.
    }
}, Zettai\Application::EARLY_EVENT);

// Задаём рутинг и контроллеры.

// Главная страница.
$app->get('/{page}', function ($page) use ($app) {
    $exerciseCount = $app['model']->getExerciseCount(false);
    $perPage = 20;
    if (($page - 1) * $perPage > $exerciseCount) {
        return $app->redirect($app['url_generator']->generate('main', ['page' => 1]));
    }
    $exerciseList = $app['model']->getExerciseList(($page - 1) * $perPage, $perPage, false);
    
    return $app->render('main.twig', [
        'exerciseList'  => $exerciseList,
        'exerciseCount' => $exerciseCount,
        'curPage'       => $page,
        'perPage'       => $perPage,
    ]);
})
->assert ('page', '\\d*')
->value  ('page', '1')
->convert('page', function ($page) {
    $page = intval ($page);
    if ($page < 1) {
        $page = 1;
    }
    return $page;
})
->bind('main');

// Просмотр одной задачи на сайте.
$app->get('/exercise/{exercise_id}', function (Request $request, $exercise_id) use ($app) {
    $exercise = $app['model']->getExercise($exercise_id);
    if ($exercise->is_hidden) {
        $exercise = null;
    }
    $page = $request->query->get('page');
    return $app->render('exercise.twig', [
        'exercise' => $exercise,
        'page'     => $page,
    ]);
})
->assert('exercise_id', '\\d+')
->convert('exercise_id', function ($exercise_id) {
    $exercise_id = intval ($exercise_id);
    if ($exercise_id < 1) {
        throw new Exception('Exercise id must be positive integer');
    }
    return $exercise_id;
})
->bind('exercise');

// Вход в админку.
$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

$app->mount('/admin', new Zettai\Controller\Admin());

// На дев-хосте добавляем генератор паролей.
if ($app['debug']) {
    $app->get('/password/{password}/{salt}', function ($password, $salt) use ($app) {
        return $app['security.encoder.digest']->encodePassword($password, $salt);
    })
    ->value('salt', '');
}

// Запускаем приложение.

$app->run();

print '<!-- server time: ' . (microtime(true) - $time) . ' -->';
