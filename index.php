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
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => TEMPLATE_DIR,
]);
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

    // константы //
    
    $twig->addGlobal('ABCS', array_keys(Zettai\Exercise::$ABCS));

    // фильтры //
    
    $twig->addFilter('wind', new \Twig_Filter_Function(function ($wind) use ($app) {
        return $app['types']->wind->from($wind)->toRussian();
    }));
    $twig->addFilter('kyoku', new \Twig_Filter_Function(function ($kyoku) use ($app) {
        return $app['types']->kyoku->from($kyoku)->toRussian();
    }));
    $twig->addFilter(new Twig_SimpleFilter('lpad', function ($input, $char, $length) {
        return str_pad($input, $length, $char, STR_PAD_LEFT);
    }));
    $twig->addFilter('tile', new \Twig_Filter_Function(function ($tiles) use ($app) {
        return $app['twig']->render('_tile.twig', ['tiles' => $tiles]);
    }));
    
    // функции //
    
    $twig->addFunction(new Twig_SimpleFunction('ceil',  function ($float) { return ceil  ($float); }));
    $twig->addFunction(new Twig_SimpleFunction('floor', function ($float) { return floor ($float); }));
    return $twig;
}));
$app['types'] = $app->share(function () {
    $service = new Zettai\Type\Service();
    $service['wind'] = $service->viewable([
        $service::ENG, $service::ENGLISH, $service::RUS, $service::RUSSIAN,
    ], [
        ['e', 'east',  'в', 'восток'],
        ['s', 'south', 'ю', 'юг'],
        ['w', 'west',  'з', 'запад'],
        ['n', 'north', 'с', 'север'],
    ]);
    $service['kyoku'] = $service->product($service['wind'], '-', range(1, 4));
    /*
    $service['suit'] = $service->viewable([
        $service::ENG, $service::ENGLISH, $service::RUS, $service::RUSSIAN,
    ], [
        ['m', 'man', 'м', 'ман'],
        ['p', 'pin', 'п', 'пин'],
        ['s', 'sou', 'с', 'со'],
    ]);
    $service['dragon'] = $service->viewable([
        $service::ENG, $service::RUS, $service::RUSSIAN,
    ], [
        ['5z', 'Б', 'Белый'],
        ['6z', 'З', 'Зелёный'],
        ['7z', 'К', 'Красный'],
    ]);
    $service['tile'] = $service->union(
        $service->product(
            [1, 2, 3, 4, 0, 5, 6, 7, 8, 9],
            $service['suit']
        ),
        $service['wind'],
        $service['dragon']
    );
    $service['hand'] = $service->iteration($service['tile'])
    ->setFromView(function ($view, $primitive) {
        // Описываем алгоритм построения примитивного значения из представления.
    })
    ->setToView(function ($view, $primitive) {
        // Описываем алгоритм построения представления заданного вида из примитивного значения.
    });
    */
    return $service;
});
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

// Главная страница админки.
$app->get('/admin/{page}', function ($page) use ($app) {
    $exerciseCount = $app['model']->getExerciseCount(true);
    $perPage = 20;
    if (($page - 1) * $perPage > $exerciseCount) {
        return $app->redirect($app['url_generator']->generate('admin_page', ['page' => 1]));
    }
    $exerciseList = $app['model']->getExerciseList(($page - 1) * $perPage, $perPage, true);
    
    return $app->render('admin/main.twig', [
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
->bind('admin_page');

// Страница просмотра задачи в админке.
$app->get('/admin/exercise/view/{exercise_id}', function (Request $request, $exercise_id) use ($app) {
    $exercise = $app['model']->getExercise($exercise_id);
    $page = $request->query->get('page');
    return $app->render('admin/exercise/view.twig', [
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
->bind('admin_exercise_view');

// Страница редактирования задачи в админке.
$app->match('/admin/exercise/edit/{exercise_id}', function (Request $request, $exercise_id) use ($app) {

    $csrfKey = 'admin_exercise_edit_' . $exercise_id;

    // Процедура отображения формы с задачей.
    $view = function (
        $exercise,
        $errors = []
    ) use (
        $app,
        $request,
        $exercise_id,
        $csrfKey
    ) {
        return $app->render('admin/exercise/edit.twig', [
            'page'        => $request->query->get('page'),
            'exercise_id' => $exercise_id,
            'csrf'        => $app['csrf']->generate($csrfKey),
            'exercise'    => $exercise,
            'errors'      => $errors,
            'TILES'       => Zettai\Tile::$TILES,
        ]);
    };
    
    // Процедура редиректа на старую форму с сохранением полей и выводом ошибок.
    $redirect = function (
        $exercise,
        $errors
    ) use (
        $app,
        $request,
        $exercise_id
    ) {
        $formKey = md5(microtime(true));
        $app['session']->set($formKey, [
            'exercise' => $exercise,
            'errors' => $errors,
        ]);
        return $app->redirect(
            $app['url_generator']->generate('admin_exercise_edit', ['exercise_id' => $exercise_id]) .
            '?page='    . $request->query->get('page') .
            '&formKey=' . $formKey
        );
    };
    
    // Обработать присланную форму.
    if ($request->getMethod() === 'POST') {
        $errors = [];
        
        // Проверить csrf-токен.
        if (! $app['csrf']->validate($request->request->get('csrf'), $csrfKey)) {
            $errors[] = 'CSRF';
        }
        
        $exercise = new Zettai\Exercise ([
            'exercise_id' => $request->request->get('exercise_id'),
            'title'     => $request->request->get('title'),
            'is_hidden' => intval($request->request->get('is_hidden')) === 1,
            'content'   => [
                'kyoku'         => $app['types']->kyoku->from($request->request->get('kyoku'))->toEnglish(),
                'position'      => $app['types']->wind->from($request->request->get('position'))->toEnglish(),
                'turn'          => $request->request->get('turn'),
                'dora'          => $request->request->get('dora'),
                'score'         => $request->request->get('score'),
                'hand'          => $request->request->get('hand'),
                'draw'          => $request->request->get('draw'),
                'discard_a'     => $request->request->get('discard_a'),
                'answer_a'      => $request->request->get('answer_a'),
                'discard_b'     => $request->request->get('discard_b'),
                'answer_b'      => $request->request->get('answer_b'),
                'discard_c'     => $request->request->get('discard_c'),
                'answer_c'      => $request->request->get('answer_c'),
                'best_answer'   => $request->request->get('best_answer'),
            ],
        ]);
        
        if ($request->request->get('save')) {
            // Попросили сохранить задачу.
            
            // Проверить поля.
            // TODO: Прикрутить валидатор.
            if (! preg_match ('/\\d{1,3}/', $exercise->exercise_id)) {
                $errors[] = 'EXERCISE_ID:NOT_A_NUMBER';
            } else {
                if (! ($exercise->exercise_id > 0)) {
                    $errors[] = 'EXERCISE_ID:NOT_POSITIVE';
                } else {
                    // Если новый номер не равен старому, то проверить, что задачи с новым номером ещё не существует.
                    if ($exercise_id !== $exercise->exercise_id && $app['model']->getExercise($exercise->exercise_id)) {
                        $errors[] = 'EXERCISE_ID:ALREADY_EXISTS';
                    }
                }
            }
            if (empty ($exercise->title)) {
                $errors[] = 'TITLE:EMPTY';
            }
            
            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty ($errors)) {
                return $redirect ($exercise, $errors);
            }
            
            // Создать задачу.
            $app['model']->setExercise($exercise);
            
            // Если старый номер не равен new, то после создания нового надо удалить старое.
            if ($exercise_id !== 'new' && $exercise_id !== $exercise->exercise_id) {
                $app['model']->deleteExercise($exercise_id);
            }
            
            // Показать новую задачу в админке.
            return $app->redirect(
                $app['url_generator']->generate('admin_exercise_view', ['exercise_id' => $exercise->exercise_id]) .
                '?page=' . $request->query->get('page')
            );
        } elseif ($request->request->get('delete')) {
            // Попросили удалить задачу.
            
            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty ($errors)) {
                return $redirect ($exercise, $errors);
            }
            
            // Удалить задачу.
            $app['model']->deleteExercise($exercise_id);
            
            // Показать список задач.
            return $app->redirect($app['url_generator']->generate('admin_page', ['page' => $request->query->get('page')]));
        }
    }
    
    // Отобразить старую форму после редиректа.
    $formKey = $request->query->get('formKey');
    if ($formKey) {
        $data = $app['session']->get($formKey);
        return $view($data['exercise'], $data['errors']);
    }
    
    // Отобразить свежую форму для новой задачи.
    if ($exercise_id === 'new') {
        return $view (new Zettai\Exercise (['exercise_id' => $app['model']->getExerciseNextId()]));
    }
    
    // Существует ли запрошенная задача?
    $exercise = $app['model']->getExercise($exercise_id);
    if (! $exercise) {
        return $view (null, ['EXERCISE:DOES_NOT_EXIST']);
    }
    
    // Отобразить свежую форму для старой задачи.
    return $view ($exercise);
})
->assert('exercise_id', '\\d+|new')
->convert('exercise_id', function ($exercise_id) {
    if ($exercise_id === 'new') {
        return $exercise_id;
    }
    $exercise_id = intval ($exercise_id);
    if ($exercise_id < 1) {
        throw new Exception('Exercise id must be "new" or positive integer');
    }
    return $exercise_id;
})
->method('GET|POST')
->bind('admin_exercise_edit');

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
