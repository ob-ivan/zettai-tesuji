<?php
namespace Zettai\Controller;

use DomDocument;
use DomXPath;
use Exception as AnyException;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Import implements ControllerProviderInterface
{
    // const //
    
    const IMPORT_XPATH_POSTS   = '/html/body/form[@id="delete_form"]/div[@class="thread"]/table[substring(@id,1,5)="post_"]';
    const IMPORT_XPATH_MESSAGE = 'tbody/tr/td[@class="reply"]/div[@class="postbody"]/div[@class="message"]';
    const IMPORT_KEY_ID       = __LINE__;
    const IMPORT_KEY_EXERCISE = __LINE__;
    const IMPORT_KEY_ANSWER   = __LINE__;
    
    // public : ControllerProviderInterface //
    
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $app->error(function (Exception $exception) {
            return $this->error($exception);
        });
        $controllers->get('/', function (Request $request) {
            return $this->import($request);
        });
        return $controllers;
    }
    
    // private : error handlers //
    
    private function error(Exception $exception)
    {
        // TODO: Разобраться, почему сюда не заходит.
        switch ($exception->getCode()) {
            case Exception::IMPORT_FILENAME_EMPTY:
                return
                    'USAGE: run.sh import FILENAME' . "\n" .
                    'OR:    run.sh import URL';
            case Exception::IMPORT_FILE_UNREACHABLE:
                return 'File is unreachable: ' . $exception->getMessage();
        }
        throw $exception;
    }
    
    // private : controllers //
    
    private function import (Request $request)
    {
        $exercises = $this->getExercises(
            $this->getExerciseNodes(
                $this->getXPath(
                    $this->getContents($request)
                )
            )
        );
        
        return 'done';
    }
    
    // private : helpers //
    
    private function getContents(Request $request)
    {
        // Прочитать либо имя файла, либо адрес треда из параметров.
        $filepath = $request->query->get(0);
        if (empty ($filepath)) {
            throw new Exception('Filename is empty', Exception::IMPORT_FILENAME_EMPTY);
        }
        
        // Достать содержимое.
        try {
            $contents = file_get_contents($filepath);
        } catch (AnyException $exception) {
            throw new Exception('Could not get contents of "' . $filepath . '"', Exception::IMPORT_FILE_UNREACHABLE, $exception);
        }
        
        return $contents;
    }
    
    /**
     * Загружает содержимое в документ и возвращает объект выборки по xpath.
    **/
    private function getXPath($contents)
    {
        libxml_use_internal_errors(true);
        $document = new DomDocument();
        $document->loadHTML($contents);
        return new DomXPath($document);
    }

    /**
     *  @return [<postId> => [
     *      self::IMPORT_KEY_ID       => <exerciseId>,
     *      self::IMPORT_KEY_EXERCISE => <exerciseNode>,
     *      self::IMPORT_KEY_ANSWER   => <answerNode>,
     *  ]]
    **/
    private function getExerciseNodes(DomXPath $xpath)
    {
        $exerciseNodes = [];
        foreach ($xpath->query(self::IMPORT_XPATH_POSTS) as $post) {
            // Выбрать айдишник поста.
            if (! preg_match('/^post_(\d+)$/', $post->attributes->getNamedItem('id')->nodeValue, $matches)) {
                continue;
            }
            $postId = intval($matches[1]);
            unset ($matches);
            
            // Выбрать узел с сообщением.
            $messageNodes = $xpath->query(self::IMPORT_XPATH_MESSAGE, $post);
            if ($messageNodes->length !== 1) {
                continue;
            }
            $messageNode = $messageNodes->item(0);
            unset ($messageNodes);
            
            // Определить задачу или ответ.
            if (preg_match('/^\s*Задача №(\d{3})/', $messageNode->textContent, $matches)) {
                $excerciseMessages[$postId] = [
                    self::IMPORT_KEY_ID       => intval($matches[1]),
                    self::IMPORT_KEY_EXERCISE => $messageNode,
                ];
            } else {
                if (preg_match('/^\s*>>(\d+)\s*Правильный ответ:/s', $messageNode->textContent, $matches)) {
                    $parentPostId = intval($matches[1]);
                    if (isset($excerciseMessages[$parentPostId])) {
                        $excerciseMessages[$parentPostId][self::IMPORT_KEY_ANSWER] = $messageNode;
                    }
                }
            }
            unset ($messageNode, $matches, $postId, $parentPostId);
        }
        return $exerciseNodes;
    }

    /**
     * Разбирает тексты задач и ответов.
     *
     *  @return [<postId> => <Zettai\Exercise>]
    **/
    private function getExercises(array $nodes)
    {
        $exercises = [];
        foreach ($nodes as $postId => $node) {
            /*
                Задача №001. Где будет голова?
                Сдача: Юг-1
                Позиция: Север
                Ход: 5
                Дора: 9пин
                Очки: 28000
                Рука: 35ман 23599пин 122344со
                Набрал: 6пин
                Что сбросить?
                А. 5ман
                В. 1со
                С. 2со
            */
            if (preg_match('~^ \s*
                Задача          \s+ №\d+. \s+ (?<title>.*)  \s+
                Сдача:          \s+ (?<kyoku>\S+-\d)        \s+
                Поз[иц]{2}ия:   \s+ (?<position>\S+)        \s+
                Ход:            \s+ (?<turn>\d+)            \s+
                Дора:           \s+ (?<dora>\S+)            \s+
                Очк(?:и|ов):    \s+ (?<score>\S.*\S+)       \s+
                Рука:           \s+ (?<hand>\S.*\S)         \s+
                Набрал:         \s+ (?<draw>\S+)            \s+
                Что \s+ сбросить\? \s+
                А.              \s+ (?<discard_a>\S+)       \s+
                В.              \s+ (?<discard_b>\S+)       \s+
                С.              \s+ (?<discard_c>\S+)       \s*
            $~sxu', $node[self::IMPORT_KEY_EXERCISE]->textContent, $matches)) {
                $data = [
                    'exercise_id'   => $node[self::IMPORT_KEY_ID],
                    'title'         => $matches['title'],
                    'is_hidden'     => true,
                    'content'       => [
                        // TODO: Конвертировать все поля из текстового формата в формат базы!
                        'kyoku'     => $matches['kyoku'],
                        'position'  => $matches['position'],
                        'turn'      => $matches['turn'],
                        'dora'      => $matches['dora'],
                        'score'     => $matches['score'],
                        'hand'      => $matches['hand'],
                        'draw'      => $matches['draw'],
                        'discard_a' => $matches['discard_a'],
                        'discard_b' => $matches['discard_b'],
                        'discard_c' => $matches['discard_c'],
                    ],
                ];
                // TODO: Разобрать ответы.
            } else {
                print 'Could not recognize format in post ' . $postId . ': ' . $node[self::IMPORT_KEY_EXERCISE]->textContent . "\n\n";
            }
        }
        return $exercises;
    }
}
