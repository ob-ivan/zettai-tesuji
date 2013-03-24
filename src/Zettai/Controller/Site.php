<?php
namespace Zettai\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Site implements ControllerProviderInterface
{
    private $app;
    
    public function connect(Application $app)
    {
        $this->app = $app;
        
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/{page}', function ($page) {
            return $this->page($page);
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
        ->bind('site_page');

        // Просмотр одной задачи на сайте.
        $controllers->get('/exercise/{exercise_id}', function (Request $request, $exercise_id) {
            return $this->exercise($request, $exercise_id);
        })
        ->assert('exercise_id', '\\d+')
        ->convert('exercise_id', function ($exercise_id) {
            $exercise_id = intval ($exercise_id);
            if ($exercise_id < 1) {
                throw new Exception('Exercise id must be positive integer');
            }
            return $exercise_id;
        })
        ->bind('site_exercise');

        return $controllers;
    }
    
    // private //
    
    private function page($page)
    {
        $exerciseCount = $this->app['model']->getExerciseCount(false);
        $perPage = 20;
        if (($page - 1) * $perPage > $exerciseCount) {
            return $this->app->redirect($this->app['url_generator']->generate('site_page', ['page' => 1]));
        }
        $exerciseList = $this->app['model']->getExerciseList(($page - 1) * $perPage, $perPage, false);
        
        return $this->app->render('site/page.twig', [
            'exerciseList'  => $exerciseList,
            'exerciseCount' => $exerciseCount,
            'curPage'       => $page,
            'perPage'       => $perPage,
        ]);
    }
    
    private function exercise(Request $request, $exercise_id)
    {
        $exercise = $this->app['model']->getExercise($exercise_id);
        if ($exercise->is_hidden) {
            $exercise = null;
        }
        $page = $request->query->get('page');
        return $this->app->render('site/exercise.twig', [
            'exercise' => $exercise,
            'page'     => $page,
        ]);
    }
}

