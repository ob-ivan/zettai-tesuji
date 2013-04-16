<?php
/**
 * Операции по обновлению базы данных при выгрузке или по крону.
**/
namespace Zettai\Controller\Console;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Import implements ControllerProviderInterface
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
                print '.';
                $exercise->content['is_answered'] = true;
                $entity->set($exercise);
            }
            print ' done!' . "\n";
        }
        return 'Done.';
    }
}
