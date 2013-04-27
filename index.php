<?php

// Подготовка глобального состояния.

$time = microtime(true);
mb_internal_encoding('utf-8');
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Пути.

define ('DOCUMENT_ROOT', __DIR__);
define ('AUTOLOAD_PATH',    DOCUMENT_ROOT . '/vendor/autoload.php');
define ('DEPLOY_LOCK_PATH', DOCUMENT_ROOT . '/deploy.lock');
define ('ERROR_DIR',        DOCUMENT_ROOT . '/error');
define ('LOG_DIR',          DOCUMENT_ROOT . '/log');
define ('TEMPLATE_DIR',     DOCUMENT_ROOT . '/template');

// Зависимости.

require_once AUTOLOAD_PATH;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

// Инициализируем приложение.

$app = new Zettai\Application(new Zettai\Config(DOCUMENT_ROOT));

$app['answer_compiler'] = $app->share(function () {
    return new Zettai\AnswerCompiler\Service();
});
$app['csrf'] = $app->share(function () use ($app) {
    return new Zettai\CsrfHandler($app['session']);
});
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile'   => LOG_DIR . (
        $app['debug']
        ? '/debug.log'
        : '/error.log'
    ),
    'monolog.level'     =>
        $app['debug']
        ? Logger::DEBUG
        : Logger::ERROR,
    'monolog.name'      => 'zettai-tesuji',
]);
$app->register(new Zettai\Provider\ParameterServiceProvider([
    'page' => [
        'assert'  => '\\d*',
        'value'   => '1',
        'convert' => function ($page) {
            $page = intval($page);
            if ($page < 1) {
                $page = 1;
            }
            return $page;
        },
    ],
    'exercise_id' => [
        'assert'  => '\\d+',
        'convert' => function ($exercise_id) {
            $exercise_id = intval($exercise_id);
            if ($exercise_id < 1) {
                throw new Exception('Exercise id must be positive integer');
            }
            return $exercise_id;
        },
    ],
    'exercise_id_new' => [
        'assert' => '\\d+|new',
        'convert' => function ($exercise_id) {
            if ($exercise_id === 'new') {
                return $exercise_id;
            }
            $exercise_id = intval($exercise_id);
            if ($exercise_id < 1) {
                throw new Exception('Exercise id must be "new" or positive integer');
            }
            return $exercise_id;
        },
    ],
]));
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
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// TODO: Научиться обращаться с валидатором.
// $app->register(new Silex\Provider\ValidatorServiceProvider());

// Если стоит режим заглушки, то выводим её и больше ничего не делаем.
$app->before(function (Request $request) use ($app) {
    if ($app['config']->dummy || file_exists(DEPLOY_LOCK_PATH)) {
        if ($request->getMethod() === 'GET') {
            return $app->render('dummy.twig');
        }
        // TODO: Обработать POST-запросы.
    }
}, Zettai\Application::EARLY_EVENT);

// Задаём рутинг и контроллеры.

$app->mount('/',        new Zettai\Controller\Web\Site());
$app->mount('/admin',   new Zettai\Controller\Web\Admin());

// Вход в админку.
$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

// На дев-хосте добавляем генератор паролей.
if ($app['debug']) {
    $app->get('/password/{password}/{salt}', function ($password, $salt) use ($app) {
        return $app['security.encoder.digest']->encodePassword($password, $salt);
    })
    ->value('salt', '');
}

// Запускаем приложение -- слегка модифицированная копипаста из Application->run().
try {
    $request = Request::createFromGlobals();
    $response = $app->handle($request);
} catch (Exception $e) {

    // Построить представление исключения.
    $lines = [];
    for (; $e; $e = $e->getPrevious()) {
        $lines[] = 'Uncaught exception ' . get_class($e) . '(' . $e->getCode() . ') "' . $e->getMessage() . '"';
        $lines[] = "<pre>\n" . $e->getTraceAsString() . "</pre>";
    }
    $presentation = implode("\n\n", $lines);

    // Вывести исключение куда надо в зависимости от режима.
    if ($app['debug']) {
        print $presentation;
    } else {
        $app['monolog']->addError($presentation);
        header('HTTP/1.1 500 Internal Server Error');
        readfile(ERROR_DIR . '/500.html');
    }
    die;
}

// Выводим ответ и подставляем время выполнения.
// TODO: Отдать бенчмаркинг на откуп FirePHP.

$content = $response->getContent();
$search = '~SERVER_TIME~';
if (strpos($content, $search)) {
    $response->setContent(str_replace($search, microtime(true) - $time, $content));
}
$response->send();
$app->terminate($request, $response);
die;
