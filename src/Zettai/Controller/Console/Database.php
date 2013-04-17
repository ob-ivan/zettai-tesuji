<?php
/**
 * Операции по обновлению базы данных при выгрузке или по крону.
**/
namespace Zettai\Controller\Console;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Zettai\Exercise;

class Database implements ControllerProviderInterface
{
    // var //

    private $model;

    // public : ControllerProviderInterface //

    public function connect(Application $app)
    {
        $this->model = $app['model'];

        $controllers = $app['controllers_factory'];
        $controllers->get('/set_is_answered', function () {
            return $this->set_is_answered();
        });
        return $controllers;
    }

    // private : controllers //

    /**
     * Проставляет всем задачам в базе флаг content.is_unasnwered значнием "истина".
    **/
    private function set_is_answered()
    {
        $entity = $this->model->exercise;
        $count  = $entity->getCount(true);
        $perPage = 20;
        $totalPages = ceil($count / $perPage);
        for ($pageNum = 0; $pageNum < $totalPages; ++$pageNum) {
            print 'Page #' . $pageNum;
            foreach ($entity->getList($pageNum * $perPage, $perPage, true) as $exercise) {

                // Так работать не должно, поскольку рекорды неизменяемы:
                // $exercise->content['is_answered'] = true;

                // Правильный интерфейс с точки зрения значений типов:
                /*
                $modified = $exercise->modify([
                    'content' => $content->modify([
                        'is_answered' => true,
                    ])
                ]);
                */

                // Пока рекорд задачи не переведён на тип, приходится обходиться полумерами:
                $modified = $exercise->modify([
                    'content' => $this->modifyContent($exercise->content, [
                        'is_answered' => true,
                    ])
                ]);

                $entity->set($modified);
                print '.';
            }
            print ' done!' . "\n";
        }
        return 'Done.';
    }

    // private : helpers //

    /**
     *  @param  [key => value]  $content
     *  @param  [key => value]  $modifications
     *  @return [key => value]
    **/
    private function modifyContent(array $content, array $modifications) {
        $modified = [];
        foreach ($content as $key => $value) {
            $modified[$key] = $value;
        }
        foreach ($modifications as $key => $value) {
            $modified[$key] = $value;
        }
        return $modified;
    }

    private function printExercise(Exercise $exercise)
    {
        $lines = [];
        $lines[] = 'exercise_id = ' . $exercise->exercise_id;
        $lines[] = 'title       = ' . $exercise->title;
        $lines[] = 'is_hidden   = ' . $exercise->is_hidden;
        $lines[] = 'content     = ' . print_r($exercise->content, true);
        return implode("\n", $lines);
    }
}
