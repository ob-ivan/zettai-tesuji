<?php
namespace Zettai\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Zettai\Exercise;
use Zettai\Tile;

class Admin implements ControllerProviderInterface
{
    // var //
    
    private $app;
    
    // public : ControllerProviderInterface //
    
    public function connect(Application $app)
    {
        $this->app = $app;
        
        $controllers = $app['controllers_factory'];
        
        // Главная страница админки.
        $controllers->get('/{page}', function ($page) {
            return $this->page($page);
        })
        ->assert ('page', '\\d*')
        ->value  ('page', '1')
        ->convert('page', function ($page) {
            $page = intval($page);
            if ($page < 1) {
                $page = 1;
            }
            return $page;
        })
        ->bind('admin_page');

        // Страница просмотра задачи в админке.
        $controllers->get('/exercise/view/{exercise_id}', function (Request $request, $exercise_id) {
            return $this->exerciseView($request, $exercise_id);
        })
        ->assert('exercise_id', '\\d+')
        ->convert('exercise_id', function ($exercise_id) {
            $exercise_id = intval ($exercise_id);
            if ($exercise_id < 1) {
                throw new Exception('Exercise id must be positive integer');
            }
            return $exercise_id;
        })
        ->bind('admin_exercise_view');

        // Страница редактирования задачи в админке.
        $controllers->match('/exercise/edit/{exercise_id}', function (Request $request, $exercise_id) {
            return $this->exerciseEdit($request, $exercise_id);
        })
        ->assert('exercise_id', '\\d+|new')
        ->convert('exercise_id', function ($exercise_id) {
            if ($exercise_id === 'new') {
                return $exercise_id;
            }
            $exercise_id = intval ($exercise_id);
            if ($exercise_id < 1) {
                throw new Exception('Exercise id must be "new" or positive integer');
            }
            return $exercise_id;
        })
        ->method('GET|POST')
        ->bind('admin_exercise_edit');

        return $controllers;
    }
    
    // private : controllers //
    
    private function page ($page)
    {
        $exerciseCount = $this->app['model']->getExerciseCount(true);
        $perPage = 20;
        if (($page - 1) * $perPage > $exerciseCount) {
            return $this->app->redirect($this->app['url_generator']->generate('admin_page', ['page' => 1]));
        }
        $exerciseList = $this->app['model']->getExerciseList(($page - 1) * $perPage, $perPage, true);
        
        return $this->app->render('admin/main.twig', [
            'exerciseList'  => $exerciseList,
            'exerciseCount' => $exerciseCount,
            'curPage'       => $page,
            'perPage'       => $perPage,
        ]);
    }
    
    private function exerciseView(Request $request, $exercise_id)
    {
        $exercise = $this->app['model']->getExercise($exercise_id);
        $page = $request->query->get('page');
        return $this->app->render('admin/exercise/view.twig', [
            'exercise' => $exercise,
            'page'     => $page,
        ]);
    }
    
    private function exerciseEdit(Request $request, $exercise_id)
    {
        $csrfKey = 'admin_exercise_edit_' . $exercise_id;

        // Процедура редиректа на старую форму с сохранением полей и выводом ошибок.
        $redirect = function (
            $exercise,
            $errors
        ) use (
            $request,
            $exercise_id
        ) {
            $formKey = md5(microtime(true));
            $this->app['session']->set($formKey, [
                'exercise' => $exercise,
                'errors' => $errors,
            ]);
            return $this->app->redirect(
                $this->app['url_generator']->generate('admin_exercise_edit', ['exercise_id' => $exercise_id]) .
                '?page='    . $request->query->get('page') .
                '&formKey=' . $formKey
            );
        };
        
        // Обработать присланную форму.
        if ($request->getMethod() === 'POST') {
            $errors = [];
            
            // Проверить csrf-токен.
            if (! $this->app['csrf']->validate($request->request->get('csrf'), $csrfKey)) {
                $errors[] = 'CSRF';
            }
            
            $exercise = new Exercise ([
                'exercise_id' => $request->request->get('exercise_id'),
                'title'     => $request->request->get('title'),
                'is_hidden' => intval($request->request->get('is_hidden')) === 1,
                'content'   => [
                    'kyoku'         => $this->app['types']->kyoku->from($request->request->get('kyoku'))->toEnglish(),
                    'position'      => $this->app['types']->wind->from($request->request->get('position'))->toEnglish(),
                    'turn'          => $request->request->get('turn'),
                    'dora'          => $this->app['types']->tile->from($request->request->get('dora'))->toTile(),
                    'score'         => $request->request->get('score'),
                    'hand'          => $request->request->get('hand'),
                    'draw'          => $request->request->get('draw'),
                    'discard_a'     => $request->request->get('discard_a'),
                    'answer_a'      => $request->request->get('answer_a'),
                    'discard_b'     => $request->request->get('discard_b'),
                    'answer_b'      => $request->request->get('answer_b'),
                    'discard_c'     => $request->request->get('discard_c'),
                    'answer_c'      => $request->request->get('answer_c'),
                    'best_answer'   => $request->request->get('best_answer'),
                ],
            ]);
            
            if ($request->request->get('save')) {
                // Попросили сохранить задачу.
                
                // Проверить поля.
                // TODO: Прикрутить валидатор.
                if (! preg_match ('/\\d{1,3}/', $exercise->exercise_id)) {
                    $errors[] = 'EXERCISE_ID:NOT_A_NUMBER';
                } else {
                    if (! ($exercise->exercise_id > 0)) {
                        $errors[] = 'EXERCISE_ID:NOT_POSITIVE';
                    } else {
                        // Если новый номер не равен старому, то проверить, что задачи с новым номером ещё не существует.
                        if ($exercise_id !== $exercise->exercise_id && $this->app['model']->getExercise($exercise->exercise_id)) {
                            $errors[] = 'EXERCISE_ID:ALREADY_EXISTS';
                        }
                    }
                }
                if (empty ($exercise->title)) {
                    $errors[] = 'TITLE:EMPTY';
                }
                
                // Если есть ошибки, редиректнуть на форму и показать ошибки.
                if (! empty ($errors)) {
                    return $redirect ($exercise, $errors);
                }
                
                // Создать задачу.
                $this->app['model']->setExercise($exercise);
                
                // Если старый номер не равен new, то после создания нового надо удалить старое.
                if ($exercise_id !== 'new' && $exercise_id !== $exercise->exercise_id) {
                    $this->app['model']->deleteExercise($exercise_id);
                }
                
                // Показать новую задачу в админке.
                return $this->app->redirect(
                    $this->app['url_generator']->generate('admin_exercise_view', ['exercise_id' => $exercise->exercise_id]) .
                    '?page=' . $request->query->get('page')
                );
            } elseif ($request->request->get('delete')) {
                // Попросили удалить задачу.
                
                // Если есть ошибки, редиректнуть на форму и показать ошибки.
                if (! empty ($errors)) {
                    return $redirect ($exercise, $errors);
                }
                
                // Удалить задачу.
                $this->app['model']->deleteExercise($exercise_id);
                
                // Показать список задач.
                return $this->app->redirect($this->app['url_generator']->generate('admin_page', ['page' => $request->query->get('page')]));
            }
        }
        
        // Процедура отображения формы с задачей.
        $view = function (
            $exercise,
            $errors = []
        ) use (
            $request,
            $exercise_id,
            $csrfKey
        ) {
            return $this->app->render('admin/exercise/edit.twig', [
                'page'        => $request->query->get('page'),
                'exercise_id' => $exercise_id,
                'csrf'        => $this->app['csrf']->generate($csrfKey),
                'exercise'    => $exercise,
                'errors'      => $errors,
                'TILES'       => Tile::$TILES,
            ]);
        };
        
        // Отобразить старую форму после редиректа.
        $formKey = $request->query->get('formKey');
        if ($formKey) {
            $data = $this->app['session']->get($formKey);
            return $view($data['exercise'], $data['errors']);
        }
        
        // Отобразить свежую форму для новой задачи.
        if ($exercise_id === 'new') {
            return $view(new Exercise (['exercise_id' => $this->app['model']->getExerciseNextId()]));
        }
        
        // Существует ли запрошенная задача?
        $exercise = $this->app['model']->getExercise($exercise_id);
        if (! $exercise) {
            return $view(null, ['EXERCISE:DOES_NOT_EXIST']);
        }
        
        // Отобразить свежую форму для старой задачи.
        return $view($exercise);
    }
}
