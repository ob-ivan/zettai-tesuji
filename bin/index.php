<?php

// Настраиваем окружение.
define ('DOCUMENT_ROOT', dirname(__DIR__));
define ('AUTOLOAD_PATH', DOCUMENT_ROOT . '/vendor/autoload.php');

set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Подключаем зависимости.
require_once AUTOLOAD_PATH;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

// Инициализируем приложение.

$app = new Zettai\Application(new Zettai\Config(DOCUMENT_ROOT));

// Задаём рутинг и контроллеры.

$app->get('/import', function (Request $request) use ($app) {
    // Прочитать либо имя файла, либо адрес треда из параметров.
    $filepath = $request->query->get(0);
    if (empty ($filepath)) {
        return
            'USAGE: run.sh import FILENAME' . "\n" .
            'OR:    run.sh import URL';
    }
    // Достать содержимое.
    try {
        $contents = file_get_contents($filepath);
    } catch (Exception $e) {
        return 'Could not get contents of "' . $filepath . '": ' .
            get_class($e) . ' (' . $e->getCode() . ') "' . $e->getMessage() . '"';
    }
    unset($filepath);
    // Разобрать содержимое.
    return 'done';
});

// Запускаем приложение с параметрами командной строки.

$request = Request::create('/' . $argv[1], 'GET', array_slice($argv, 2));
try {
    $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
} catch (Exception $e) {
    print 'Uncaught exception ' . get_class($e) . ' (' . $e->getCode() . '): "' . $e->getMessage() . '"' . "\n" .
        $e->getTraceAsString() . "\n";
    die;
}
print $response->getContent() . "\n";
