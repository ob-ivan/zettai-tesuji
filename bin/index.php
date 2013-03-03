<?php

// Пути.
define ('DOCUMENT_ROOT', dirname(__DIR__));
define ('AUTOLOAD_PATH', DOCUMENT_ROOT . '/vendor/autoload.php');

// Зависимости.
require_once AUTOLOAD_PATH;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

// Инициализируем приложение.

$app = new Zettai\Application(new Zettai\Config(DOCUMENT_ROOT));

// Задаём рутинг и контроллеры.

// Запускаем приложение с параметрами командной строки.

$request = Request::create('/' . $argv[1]);
/*
$commandLine = new Zettai\CommandLine();
$request = Request::create($commandLine->get());
*/
try {
$response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
} catch (Exception $e) {
    print 'Uncaught exception ' . get_class($e) . ' (' . $e->getCode() . '): "' . $e->getMessage() . '"' . "\n" .
        $e->getTraceAsString() . "\n";
    die;
}
print $response->getContent();
