<?php
namespace Zettai\Controller;

use Exception as AnyException;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Import implements ControllerProviderInterface
{
    // public : ControllerProviderInterface //
    
    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];
        $controllers->get('/', function (Request $request) use  {
            return $this->import($request);
        });
        return $controllers;
    }
    
    // private //
    
    private function import (Request $request)
    {
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
        } catch (AnyException $e) {
            throw new Exception('Could not get contents of "' . $filepath . '"', Exception::IMPORT_FILE_UNREACHABLE, $e);
        }
        unset($filepath);
        
        // Загрузить содержимое в документ.
        libxml_use_internal_errors(true);
        $document = new DomDocument();
        $document->loadHTML($contents);
        $xpath = new DomXPath($document);
        $posts = $xpath->query('/html/body/form[@id="delete_form"]/div[@class="thread"]/table[substring(@id,1,5)="post_"]');
        unset($document);
        
        // Найти посты с задачами и ответами.
        /**
         * [<postId> => ['exerciseId' => <exerciseId>, 'messageNode' => <messageNode>]]
        **/
        $excerciseMessages = [];
        /**
         * [<exerciseId> => <messageNode>]
        **/
        $answerMessages = [];
        foreach ($posts as $post) {
            // Выбрать айдишник поста.
            if (! preg_match('/^post_(\d+)$/', $post->attributes->getNamedItem('id')->nodeValue, $matches)) {
                continue;
            }
            $postId = intval($matches[1]);
            unset ($matches);
            
            // Выбрать узел с сообщением.
            $messageNodes = $xpath->query('tbody/tr/td[@class="reply"]/div[@class="postbody"]/div[@class="message"]', $post);
            if ($messageNodes->length !== 1) {
                continue;
            }
            $messageNode = $messageNodes->item(0);
            unset ($messageNodes);
            
            // Определить задачу или ответ.
            if (preg_match('/^\s*Задача №(\d{3})/', $messageNode->textContent, $matches)) {
                $excerciseMessages[$postId] = [
                    'exerciseId'  => intval($matches[1]),
                    'messageNode' => $messageNode,
                ];
            } else {
                if (preg_match('/^\s*>>(\d+)\s*Правильный ответ:/s', $messageNode->textContent, $matches)) {
                    $parentPostId = intval($matches[1]);
                    if (isset($excerciseMessages[$parentPostId])) {
                        $answerMessages[$excerciseMessages[$parentPostId]['exerciseId']] = $messageNode;
                    }
                }
            }
            unset ($messageNode, $matches, $postId, $parentPostId);
        }
        unset($xpath, $posts, $post);
        
        // Разобрать тексты задач и ответов.
        /**
         * [<postId> => <Zettai\Exercise>]
        **/
        $exercises = [];
        foreach ($excerciseMessages as $postId => $exerciseMessage) {
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
            $~sxu', $exerciseMessage['messageNode']->textContent, $matches)) {
                $data = [
                    'exercise_id'   => $exerciseMessage['exerciseId'],
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
                print 'Could not recognize format in post ' . $postId . ': ' . $exerciseMessage['messageNode']->textContent . "\n\n";
            }
        }
        return 'done';
    }
}
