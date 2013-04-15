<?php
namespace Zettai\Controller\Web;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Site implements ControllerProviderInterface
{
    // const //

    const PER_PAGE = 20;

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
        $exerciseCount = $this->app['model']->exercise->getCount(false);
        if (($page - 1) * self::PER_PAGE > $exerciseCount) {
            return $this->app->redirect($this->app['url_generator']->generate('site_page', ['page' => 1]));
        }
        $exerciseList = $this->app['model']->exercise->getList(($page - 1) * self::PER_PAGE, self::PER_PAGE, false);

        return $this->app->render('site/page.twig', [
            'exerciseList'  => $exerciseList,
            'exerciseCount' => $exerciseCount,
            'curPage'       => $page,
            'perPage'       => self::PER_PAGE,
        ]);
    }

    private function exercise(Request $request, $exercise_id)
    {
        $exercise = $this->app['model']->exercise->get($exercise_id);
        if ($exercise && $exercise->is_hidden) {
            $exercise = null;
        }
        $page = $this->app['model']->exercise->getPage($exercise_id, self::PER_PAGE);
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
        if (! $this->app['csrf']->validate($request->request->get('csrf'), $this->csrfKey($exercise_id))) {
            $errors[] = 'CSRF';
        }
        $exercise = $this->app['model']->exercise->get($exercise_id);
        if (! $exercise || $exercise->is_hidden) {
            $errors[] = 'EXERCISE:DOES_NOT_EXIST';
        } elseif (! $exercise->content['is_answered']) {
            $errors[] = 'EXERCISE:NOT_ANSWERED';
        }
        if (! empty($errors)) {
            return $this->app->json(['errors' => $errors]);
        }

        // Скомпилировать ответы и получить номер следующей задачи.
        $answers = [];
        foreach ($exercise->content['answer'] as $letter => $answer) {
            $answers[$letter] = [
                // TODO: Устранить дублирование с TwigServiceProvider/addFilter('tile').
                'discard' => $this->app['twig']->render('_tile.twig', ['tiles' => $answer['discard']]),
                'comment' => $this->app['answer_compiler']->compile($answer['comment']),
            ];
        }
        $nextId = $this->app['model']->exercise->getNextId($exercise_id);

        // Собрать выходной массив.
        $data = [
            'answers'       => $answers,
            'best_answer'   => $exercise->content['best_answer'],
        ];
        if ($nextId) {
            $data['exercise_next'] = $this->app['url_generator']->generate(
                'site_exercise',
                ['exercise_id' => $this->app['model']->exercise->getNextId($exercise_id)]
            );
        }

        return $this->app->json($data);
    }

    // private : helpers //

    private function csrfKey($exercise_id)
    {
        return 'site_exercise_answer_' . $exercise_id;
    }
}
