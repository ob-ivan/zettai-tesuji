<?php
namespace Zettai\Controller;

use DomDocument, DomText, DomXPath;
use Exception as AnyException;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Zettai\Exercise;

class Import implements ControllerProviderInterface
{
    // const //
    
    const IMPORT_XPATH_POSTS   = '/html/body/form[@id="delete_form"]/div[@class="thread"]/table[substring(@id,1,5)="post_"]';
    const IMPORT_XPATH_MESSAGE = 'tbody/tr/td[@class="reply"]/div[@class="postbody"]/div[@class="message"]';
    
    const IMPORT_KEY_ID       = __LINE__;
    const IMPORT_KEY_EXERCISE = __LINE__;
    const IMPORT_KEY_ANSWER   = __LINE__;
    
    // var //
    
    private $model;
    private $types;
    
    // public : ControllerProviderInterface //
    
    public function connect(Application $app)
    {
        $this->model = $app['model'];
        $this->types = $app['types'];
        
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
        
        foreach ($exercises as $exercise) {
            $this->model->setExercise($exercise);
        }
        
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
            if (preg_match('/^\s*Задача №(\d{3})/u', $messageNode->textContent, $matches)) {
                $exerciseNodes[$postId] = [
                    self::IMPORT_KEY_ID       => intval($matches[1]),
                    self::IMPORT_KEY_EXERCISE => $messageNode,
                ];
            } else {
                if (preg_match('/^\s*>>(\d+)\s*Правильный ответ:/su', $messageNode->textContent, $matches)) {
                    $parentPostId = intval($matches[1]);
                    if (isset($exerciseNodes[$parentPostId])) {
                        $exerciseNodes[$parentPostId][self::IMPORT_KEY_ANSWER] = $messageNode;
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
     *  @param  [<postId> => [
     *      self::IMPORT_KEY_ID       => <exerciseId>,
     *      self::IMPORT_KEY_EXERCISE => <exerciseNode>,
     *      self::IMPORT_KEY_ANSWER   => <answerNode>,
     *  ]]  $nodes
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
                ];
                // TODO: Убрать явные приведения к типам, когда для content будет определено представление.
                $content = [
                    'kyoku'     => $this->types->kyoku->from(mb_strtolower($matches['kyoku']))->toEnglish(),
                    'position'  => $this->types->wind ->from(mb_strtolower($matches['position']))->toEnglish(),
                    'turn'      => $matches['turn'],
                    'dora'      => $this->types->tile ->from($matches['dora'])->toEnglish(),
                    'score'     => $matches['score'],
                    'hand'      => $this->types->tileSequence->fromRussian($matches['hand'])->toTile(),
                    'draw'      => $matches['draw'],
                    'answer'    => [
                        'a' => [
                            'discard' => $matches['discard_a'],
                            'comment' => null,
                        ],
                        'b' => [
                            'discard' => $matches['discard_b'],
                            'comment' => null,
                        ],
                        'c' => [
                            'discard' => $matches['discard_c'],
                            'comment' => null,
                        ],
                    ],
                    'best_answer' => null,
                ];
                
                // Разобрать ответы.
                if (! isset($node[self::IMPORT_KEY_ANSWER])) {
                    print 'Не найден пост с ответом к задаче ' . $node[self::IMPORT_KEY_ID] . "\n\n";
                } else {
                    /**
                     * <abc> ::= 'a' | 'b' | 'c' ;
                    **/
                    $bestAnswer = null;
                    $currentAnswer = null;
                    /**
                     * [<abc> => [<text>]]
                    **/
                    $answers = [];
                    $mode = 'none'; // 'best' | 'others'
                    foreach ($node[self::IMPORT_KEY_ANSWER]->childNodes as $childNode) {
                    
                        if ($childNode instanceof DomText) {
                            if (preg_match('/Правильный ответ:/u', $childNode->textContent)) {
                                $mode = 'best';
                            } elseif (preg_match('/Другие ответы:/u', $childNode->textContent)) {
                                $mode = 'others';
                            }
                        } elseif ($childNode->tagName === 'span' && $childNode->attributes->getNamedItem('class')->nodeValue === 'spoiler') {
                            if (preg_match('/^\s*([АВС]). (\w+). /u', $childNode->textContent, $matches)) {
                                $answer = $this->russianToEnglish($matches[1]);
                                if ($answer) {
                                    $expectedDiscard = $content['answer'][$answer]['discard'];
                                    if ($expectedDiscard === $matches[2]) {
                                        $currentAnswer = $answer;
                                        if ($mode === 'best') {
                                            $bestAnswer = $answer;
                                        }
                                        $answers[$currentAnswer] = [substr($childNode->textContent, strlen($matches[0]))];
                                    } else {
                                        print 'Discard ' . $answer . ' in post (' . $expectedDiscard . ') ' .
                                            'does not match discard in answer post (' . $matches[2] . ')' . "\n\n"
                                        ;
                                    }
                                } else {
                                    print 'Span for other answer does not contain discard: ' . $childNode->textContent . "\n\n";
                                }
                            } elseif ($currentAnswer) {
                                if (preg_match('/^\((.*)\)$/u', $childNode->textContent, $matches)) {
                                    // Примечания переводчика.
                                    $answers[$currentAnswer][] = '(*' . $matches[1] . ')';
                                } else {
                                    $answers[$currentAnswer][] = $childNode->textContent;
                                }
                            }
                        }
                    }
                    if ($bestAnswer && count($answers) === 3) {
                        foreach ($this->types->abc->each() as $letter) {
                            $content['answer'][$letter.'']['comment'] = implode (' ', $answers[$letter.'']);
                        }
                        $content['best_answer'] = $bestAnswer;
                        
                        // TODO: Сделать так, когда типы будут позволять.
                        // $exercises[$postId] = $this->types->exercise->from($data + ['content' => $content]);
                        $exercises[$postId] = new Exercise($data + ['content' => $content]);
                    } else {
                        print 'Could not recognize answers in post #' . $postId . ': ' . print_r($node[self::IMPORT_KEY_ANSWER], true) . "\n\n";
                    }
                }
            } else {
                print 'Could not recognize format in post #' . $postId . ': ' . $node[self::IMPORT_KEY_EXERCISE]->textContent . "\n\n";
            }
        }
        return $exercises;
    }
    
    private function russianToEnglish($russian)
    {
        switch ($russian) {
            case 'А': return 'a';
            case 'В': return 'b';
            case 'С': return 'c';
        }
        return null;
    }
}
