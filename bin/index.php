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
    
    // Загрузить содержимое в документ.
    libxml_use_internal_errors(true);
    $document = new DomDocument();
    $document->loadHTML($contents);
    $xpath = new DomXPath($document);
    $posts = $xpath->query('/html/body/form[@id="delete_form"]/div[@class="thread"]/table[substring(@id,1,5)="post_"]');
    
    // Найти посты с задачами и ответами.
    /**
     * [<postId> => ['exerciseId' => <exerciseid>, 'messageNode' => <messageNode>]]
    **/
    $excerciseMessages = [];
    $answerMessages = [];
    foreach ($posts as $post) {
        // Выбрать айдишник поста.
        if (! preg_matches('/^post_(\d+)$/', $post->attributes->getNamedItem('id'), $matches)) {
            continue;
        }
        $postId = intval($matches[1]);
        
        // Выбрать узел с сообщением.
        $messageNodes = $xpath->query('tbody/tr/td[@class="reply"]/div[@class="postbody"]/div[@class="message"]', $post);
        if ($messageNodes->length !== 1) {
            continue;
        }
        $messageNode = $messageNodes->item(0);
        
        // Определить задачу или ответ.
        if (preg_match('/^\s*Задача №(\d{3})/', $messageNode->textContent, $matches)) {
            $excerciseMessages[$postId] = [
                'exerciseId'  => intval($matches[1]),
                'messageNode' => $messageNode,
            ];
        } elseif (preg_match('/^\s*>>(\d+)/', $messageNode->textContent, $matches)) {
            $parentPostId = intval($matches);
            if (isset($excerciseMessages[$parentPostId])) {
                // Пост, отвечающий на задачу, может не быть ответом из учебника.
                // Надо ещё проверить формат текста.
                // TODO
            }
        }
    }
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
