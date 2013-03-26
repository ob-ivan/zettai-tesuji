<?php
namespace Zettai\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Site implements ControllerProviderInterface
{
    // var //
    
    private $app;
    
    // public : ControllerProviderInterface //
    
    public function connect(Application $app)
    {
        $this->app = $app;
        
        $controllers = $app['controllers_factory'];
        
        // Главная страница -- список задач.
        $app['parameter']->setParameters(
            $controllers->get('/{page}', function ($page) {
                return $this->page($page);
            }),
            ['page' => 'page']
        )
        ->bind('site_page');

        // Просмотр одной задачи на сайте.
        $app['parameter']->setParameters(
            $controllers->get('/exercise/{exercise_id}', function (Request $request, $exercise_id) {
                return $this->exercise($request, $exercise_id);
            }),
            ['exercise_id' => 'exercise_id']
        )
        ->bind('site_exercise');
        
        // Аякс для получения ответов к задаче.
        $app['parameter']->setParameters(
            $controllers->post('/exercise/answer/{exercise_id}', function (Request $request, $exercise_id) {
                return $this->exerciseAnswer($request, $exercise_id);
            }),
            ['exercise_id' => 'exercise_id']
        )
        ->bind('site_exercise_answer');

        return $controllers;
    }
    
    // private : controllers //
    
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
        if ($exercise && $exercise->is_hidden) {
            $exercise = null;
        }
        $page = $request->query->get('page');
        return $this->app->render('site/exercise.twig', [
            'exercise'  => $exercise,
            'page'      => $page,
            'csrf'      => $this->app['csrf']->generate($this->csrfKey($exercise_id)),
        ]);
    }
    
    private function exerciseAnswer(Request $request, $exercise_id)
    {
        // Проверить входные данные.
        $errors = [];
        $exercise = $this->app['model']->getExercise($exercise_id);
        if (! $exercise || $exercise->is_hidden) {
            $errors[] = 'EXERCISE:DOES_NOT_EXIST';
        }
        if (! $this->app['csrd']->validate($request->request->get('csrf'), $this->csrfKey($exercise_id))) {
            $errors[] = 'CSRF';
        }
        if (! empty($errors)) {
            return $this->app->json(['errors' => $errors]);
        }
        // Отдать ответы, правильный ответ, следующую задачу.
        return $this->app->json([
            'answer'            => $exercise['answer'],
            'best_answer'       => $exercise['best_answer'],
            'next_exercise_id'  => $this->app['model']->getNextExerciseId($exercise_id),
        ]);
    }
    
    // private : helpers //
    
    private function csrfKey($exercise_id)
    {
        return 'site_exercise_answer_' . $exercise_id;
    }
}

