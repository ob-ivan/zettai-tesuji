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

// Подключаем контроллеры из провайдеров.

$app->mount('/import', new Zettai\Controller\Import());

// Запускаем приложение с параметрами командной строки.

$request = Request::create('/' . $argv[1] . '/', 'GET', array_slice($argv, 2));
try {
    $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
} catch (Exception $e) {
    print 'Uncaught exception ' . get_class($e) . ' (' . $e->getCode() . '): "' . $e->getMessage() . '"' . "\n" .
        $e->getTraceAsString() . "\n";
    die;
}
print $response->getContent() . "\n";
