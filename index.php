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
$app['csrf'] = $app->share(function () use ($app) {
    return new Zettai\CsrfHandler($app['session']);
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
            'pattern' => '^/admin(/|$)',
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
    $twig->addFilter(new Twig_SimpleFilter('lpad', function ($input, $char, $length) {
        return str_pad($input, $length, $char, STR_PAD_LEFT);
    }));
    $twig->addFunction(new Twig_SimpleFunction('ceil',  function ($float) { return ceil  ($float); }));
    $twig->addFunction(new Twig_SimpleFunction('floor', function ($float) { return floor ($float); }));
    return $twig;
}));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// TODO: Научиться обращаться с валидатором.
// $app->register(new Silex\Provider\ValidatorServiceProvider());

// Задаём рутинг и контроллеры.

// Главная страница.
$app->get('/{page}', function ($page) use ($app) {
    $mondaiCount = $app['model']->getMondaiCount(false);
    $perPage = 20;
    if (($page - 1) * $perPage > $mondaiCount) {
        return $app->redirect($app['url_generator']->generate('main', ['page' => 1]));
    }
    $mondaiList = $app['model']->getMondaiList(($page - 1) * $perPage, $perPage, false);
    
    return $app->render('main.twig', [
        'mondaiList'  => $mondaiList,
        'mondaiCount' => $mondaiCount,
        'curPage'     => $page,
        'perPage'     => $perPage,
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
$app->get('/mondai/{mondai_id}', function (Request $request, $mondai_id) use ($app) {
    $mondai = $app['model']->getMondai($mondai_id);
    if ($mondai['is_hidden']) {
        $mondai = null;
    }
    $page = $request->query->get('page');
    return $app->render('mondai.twig', [
        'mondai' => $mondai,
        'page'   => $page,
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
->bind('mondai');

// Вход в админку.
$app->get('/login', function (Request $request) use ($app) {
    return $app->render('login.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

// Главная страница админки.
$app->get('/admin/{page}', function ($page) use ($app) {
    $mondaiCount = $app['model']->getMondaiCount(true);
    $perPage = 20;
    if (($page - 1) * $perPage > $mondaiCount) {
        return $app->redirect($app['url_generator']->generate('admin_page', ['page' => 1]));
    }
    $mondaiList = $app['model']->getMondaiList(($page - 1) * $perPage, $perPage, true);
    
    return $app->render('admin/main.twig', [
        'mondaiList'  => $mondaiList,
        'mondaiCount' => $mondaiCount,
        'curPage'     => $page,
        'perPage'     => $perPage,
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
$app->get('/admin/mondai/view/{mondai_id}', function (Request $request, $mondai_id) use ($app) {
    $mondai = $app['model']->getMondai($mondai_id);
    $page = $request->query->get('page');
    return $app->render('admin/mondai/view.twig', [
        'mondai' => $mondai,
        'page'   => $page,
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

// Страница редактирования задачи в админке.
$app->match('/admin/mondai/edit/{mondai_id_old}', function (Request $request, $mondai_id_old) use ($app) {

    $csrfKey = function () use ($mondai_id_old) {
        return 'admin_mondai_edit_' . $mondai_id_old;
    };

    // Процедура отображения формы с задачей.
    $view = function (
        $mondai_id_new,
        $title      = '',
        $is_hidden  = true,
        $content    = '',
        $errors     = []
    ) use (
        $app,
        $request,
        $mondai_id_old,
        $csrfKey
    ) {
        return $app->render('admin/mondai/edit.twig', [
            'page'          => $request->query->get('page'),
            'mondai_id_old' => $mondai_id_old,
            'csrf'          => $app['csrf']->generate($csrfKey()),
            'mondai_id_new' => $mondai_id_new,
            'title'         => $title,
            'is_hidden'     => $is_hidden,
            'content'       => $content,
            'errors'        => $errors,
        ]);
    };
    
    // Процедура редиректа на старую форму с сохранением полей и выводом ошибок.
    $redirect = function (
        $mondai_id_new,
        $title,
        $is_hidden,
        $content,
        $errors
    ) use (
        $app,
        $request,
        $mondai_id_old
    ) {
        $formKey = md5(microtime(true));
        $app['session']->set($formKey, [
            'mondai_id_new' => $mondai_id_new,
            'title'         => $title,
            'is_hidden'     => $is_hidden,
            'content'       => $content,
            'errors'        => $errors,
        ]);
        return $app->redirect(
            $app['url_generator']->generate('admin_mondai_edit', ['mondai_id_old' => $mondai_id_old]) .
            '?page=' . $request->query->get('page') .
            '&formKey=' . $formKey
        );
    };
    
    // Обработать присланную форму.
    if ($request->getMethod() === 'POST') {
        $errors = [];
        
        // Проверить csrf-токен.
        if (! $app['csrf']->validate($request->request->get('csrf'), $csrfKey())) {
            $errors[] = 'CSRF';
        }
        
        $mondai_id_new = $request->request->get('mondai_id_new');
        $title         = $request->request->get('title');
        $is_hidden     = intval($request->request->get('is_hidden')) === 1;
        $content       = $request->request->get('content');
            
        if ($request->request->get('save')) {
            // Попросили сохранить задачу.
            
            // Проверить поля.
            // TODO: Прикрутить валидатор.
            if (! preg_match ('/\\d{1,3}/', $mondai_id_new)) {
                $errors[] = 'MONDAI_ID_NEW:NOT_A_NUMBER';
            } else {
                $mondai_id_new = intval ($mondai_id_new);
                if (! ($mondai_id_new > 0)) {
                    $errors[] = 'MONDAI_ID_NEW:NOT_POSITIVE';
                } else {
                    // Если новый номер не равен старому, то проверить, что задачи с новым номером ещё не существует.
                    if ($mondai_id_old !== $mondai_id_new && $app['model']->getMondai($mondai_id_new)) {
                        $errors[] = 'MONDAI_ID_NEW:ALREADY_EXISTS';
                    }
                }
            }
            $title = trim ($title);
            if (empty ($title)) {
                $errors[] = 'TITLE:EMPTY';
            }
            $content = trim ($content);
            if (empty ($content)) {
                $errors[] = 'CONTENT:EMPTY';
            }
            
            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty ($errors)) {
                return $redirect (
                    $mondai_id_new,
                    $title,
                    $is_hidden,
                    $content,
                    $errors
                );
            }
            
            // Создать задачу.
            $app['model']->setMondai([
                'mondai_id' => $mondai_id_new,
                'title'     => $title,
                'is_hidden' => $is_hidden,
                'content'   => $content,
            ]);
            
            // Если старый номер не равен new, то после создания нового надо удалить старое.
            if ($mondai_id_old !== 'new' && $mondai_id_old !== $mondai_id_new) {
                $app['model']->deleteMondai($mondai_id_old);
            }
            
            // Показать новую задачу в админке.
            return $app->redirect(
                $app['url_generator']->generate('admin_mondai_view', ['mondai_id' => $mondai_id_new]) .
                '?page=' . $request->query->get('page')
            );
        } elseif ($request->request->get('delete')) {
            // Попросили удалить задачу.
            
            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty ($errors)) {
                return $redirect (
                    $mondai_id_new,
                    $title,
                    $is_hidden,
                    $content,
                    $errors
                );
            }
            
            // Удалить задачу.
            $app['model']->deleteMondai($mondai_id_old);
            
            // Показать список задач.
            return $app->redirect($app['url_generator']->generate('admin_page', ['page' => $request->query->get('page')]));
        }
    }
    
    // Отобразить старую форму после редиректа.
    $formKey = $request->query->get('formKey');
    if ($formKey) {
        $data = $app['session']->get($formKey);
        return $view(
            $data['mondai_id_new'],
            $data['title'],
            $data['is_hidden'],
            $data['content'],
            $data['errors']
        );
    }
    
    // Отобразить свежую форму для новой задачи.
    if ($mondai_id_old === 'new') {
        return $view ($app['model']->getMondaiNextId());
    }
    
    // Существует ли запрошенная задача?
    $mondai = $app['model']->getMondai($mondai_id_old);
    if (! $mondai) {
        return $view (0, '', '', ['MONDAI_ID_OLD:DOES_NOT_EXIST']);
    }
    
    // Отобразить свежую форму для старой задачи.
    return $view (
        $mondai_id_old,
        $mondai['title'],
        $mondai['is_hidden'],
        $mondai['content']
    );
})
->assert('mondai_id_old', '\\d+|new')
->convert('mondai_id_old', function ($mondai_id_old) {
    if ($mondai_id_old === 'new') {
        return $mondai_id_old;
    }
    $mondai_id_old = intval ($mondai_id_old);
    if ($mondai_id_old < 1) {
        throw new Exception('Mondai id must be "new" or positive integer');
    }
    return $mondai_id_old;
})
->method('GET|POST')
->bind('admin_mondai_edit');

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
