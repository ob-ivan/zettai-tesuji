<?php
namespace Zettai\Controller\Web;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Admin implements ControllerProviderInterface
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

        // Главная страница админки.
        $app['parameter']->setParameters(
            $controllers->get('/{page}', function ($page) {
                return $this->page($page);
            }),
            ['page' => 'page']
        )
        ->bind('admin_page');

        // Страница просмотра задачи в админке.
        $app['parameter']->setParameters(
            $controllers->get('/exercise/view/{exercise_id}', function (Request $request, $exercise_id) {
                return $this->exerciseView($request, $exercise_id);
            }),
            ['exercise_id' => 'exercise_id']
        )
        ->bind('admin_exercise_view');

        // Страница редактирования задачи в админке.
        $app['parameter']->setParameters(
            $controllers->match('/exercise/edit/{exercise_id}', function (Request $request, $exercise_id) {
                return $this->exerciseEdit($request, $exercise_id);
            }),
            ['exercise_id' => 'exercise_id_new']
        )
        ->method('GET|POST')
        ->bind('admin_exercise_edit');

        // Страница просмотра темы в админке.
        $controllers->get('/theme/view/{theme_id}', function ($theme_id) {
            return $this->themeView($theme_id);
        })
        ->bind('admin_theme_view');

        // Страница создания темы в админке.
        $controllers->get('/theme/edit/new', function () {
            return $this->themeNew();
        })
        ->bind('admin_theme_new');

        // Страница редактирования темы в админке.
        $controllers->get('/theme/edit/{theme_id}', function (Request $request, $theme_id) {
            return $this->themeEdit($request, $theme_id);
        })
        ->convert('theme_id', function ($theme_id) {
            return $this->convertThemeId($theme_id);
        })
        ->bind('admin_theme_edit');

        // Контроллер сохранения темы в админке.
        $controllers->post('/theme/save/{theme_id}', function (Request $request, $theme_id) {
            return $this->themeSave($request, $theme_id);
        })
        ->convert('theme_id', function ($theme_id) {
            return $this->convertThemeId($theme_id);
        })
        ->bind('admin_theme_save');

        return $controllers;
    }

    // private : controllers //

    private function page ($page)
    {
        $exerciseCount = $this->app['model']->exercise->getCount(true);
        if (($page - 1) * self::PER_PAGE > $exerciseCount) {
            return $this->app->redirect($this->app['url_generator']->generate('admin_page', ['page' => 1]));
        }
        $exerciseList = $this->app['model']->exercise->getList(($page - 1) * self::PER_PAGE, self::PER_PAGE, true);

        return $this->app->render('admin/main.twig', [
            'exerciseList'  => $exerciseList,
            'exerciseCount' => $exerciseCount,
            'curPage'       => $page,
            'perPage'       => self::PER_PAGE,
        ]);
    }

    private function exerciseView(Request $request, $exercise_id)
    {
        $exercise = $this->app['model']->exercise->get($exercise_id);
        $prev = $this->app['model']->exercise->getPrevId($exercise_id, true);
        $next = $this->app['model']->exercise->getNextId($exercise_id, true);
        $page = $this->app['model']->exercise->getPage($exercise_id, self::PER_PAGE, true);
        return $this->app->render('admin/exercise/view.twig', [
            'exercise'  => $exercise,
            'prev'      => $prev,
            'next'      => $next,
            'page'      => $page,
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
            $exercise_id
        ) {
            $formKey = md5(microtime(true));
            $this->app['session']->set($formKey, [
                'exercise'  => $exercise,
                'errors'    => $errors,
            ]);
            return $this->app->redirect(
                $this->app['url_generator']->generate('admin_exercise_edit', ['exercise_id' => $exercise_id]) .
                '?formKey=' . $formKey
            );
        };

        // Обработать присланную форму.
        if ($request->getMethod() === 'POST') {
            $errors = [];

            // Проверить csrf-токен.
            if (! $this->app['csrf']->validate($request->request->get('csrf'), $csrfKey)) {
                $errors[] = 'CSRF';
            }

            $exercise = $this->app['types']->exercise->fromForm([
                'exercise_id'   => $request->request->get('exercise_id'),
                'title'         => $request->request->get('title'),
                'is_hidden'     => $request->request->get('is_hidden') === '1',
                'content'       => $this->app['types']->exerciseContent->fromForm([
                    'kyoku'         => $request->request->get('kyoku'),
                    'position'      => $request->request->get('position'),
                    'turn'          => $request->request->get('turn'),
                    'dora'          => $request->request->get('dora'),
                    'score'         => $request->request->get('score'),
                    'hand'          => $request->request->get('hand'),
                    'draw'          => $request->request->get('draw'),
                    'is_answered'   => $request->request->get('is_answered') === '1',
                    'answer'        => $request->request->get('answer'),
                    'best_answer'   => $request->request->get('best_answer'),
                ]),
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
                        if ($exercise_id !== $exercise->exercise_id &&
                            $this->app['model']->exercise->get($exercise->exercise_id)
                        ) {
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
                $this->app['model']->exercise->set($exercise);

                // Если старый номер не равен new, то после создания нового надо удалить старое.
                if ($exercise_id !== 'new' && $exercise_id !== $exercise->exercise_id) {
                    $this->app['model']->exercise->delete($exercise_id);
                }

                // Показать новую задачу в админке.
                return $this->app->redirect(
                    $this->app['url_generator']->generate(
                        'admin_exercise_view',
                        ['exercise_id' => $exercise->exercise_id]
                    )
                );
            } elseif ($request->request->get('delete')) {
                // Попросили удалить задачу.

                // Если есть ошибки, редиректнуть на форму и показать ошибки.
                if (! empty ($errors)) {
                    return $redirect ($exercise, $errors);
                }

                // Заранее посчитать, на какую страницу потом перенаправить пользователя.
                $page = $this->app['model']->exercise->getPage($exercise_id, self::PER_PAGE, true);

                // Удалить задачу.
                $this->app['model']->exercise->delete($exercise_id);

                // Показать список задач.
                return $this->app->redirect(
                    $this->app['url_generator']->generate('admin_page', ['page' => $page])
                );
            }
        }

        // Процедура отображения формы с задачей.
        $view = function (
            $exercise,
            $errors = []
        ) use (
            $exercise_id,
            $csrfKey
        ) {
            return $this->app->render('admin/exercise/edit.twig', [
                'exercise_id' => $exercise_id,
                'csrf'        => $this->app['csrf']->generate($csrfKey),
                'exercise'    => $exercise,
                'errors'      => $errors,
                'TILES'       => $this->app['types']->tile->each(),
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
            return $view(
                $this->app['types']->exercise->fromNew($this->app['model']->exercise->getNewId())
            );
        }

        // Существует ли запрошенная задача?
        $exercise = $this->app['model']->exercise->get($exercise_id);
        if (! $exercise) {
            return $view(null, ['EXERCISE:DOES_NOT_EXIST']);
        }

        // Отобразить свежую форму для старой задачи.
        return $view($exercise);
    }

    private function themeView($theme_id)
    {
        $theme = $this->app['model']->theme->get($theme_id);
        $prev = $this->app['model']->theme->getPrevId($theme_id, true);
        $next = $this->app['model']->theme->getNextId($theme_id, true);
        $page = $this->app['model']->theme->getPage($theme_id, self::PER_PAGE, true);
        return $this->app->render('admin/theme/view.twig', [
            'theme'  => $theme,
            'prev'      => $prev,
            'next'      => $next,
            'page'      => $page,
        ]);
    }

    private function themeNew()
    {
        $this->log(__METHOD__ . ':' . __LINE__);

        $theme = $this->app['types']->theme->fromNew($this->app['model']->theme->getNewId());

        $this->log(__METHOD__ . ':' . __LINE__);

        $theme_id = 'new';
        $csrfKey = $this->themeGetCsrfToken($theme_id);

        $this->log(__METHOD__ . ':' . __LINE__);

        return $this->themeViewForm($theme, [], $theme_id, $csrfKey);
    }

    private function themeSave(Request $request, $theme_id)
    {
        $this->log(__METHOD__ . ':' . __LINE__);

        $errors = [];
        $csrfKey = $this->themeGetCsrfToken($theme_id);

        $this->log(__METHOD__ . ':' . __LINE__);

        // Проверить csrf-токен.
        if (! $this->app['csrf']->validate($request->request->get('csrf'), $csrfKey)) {
            $errors[] = 'CSRF';
        }

        $this->log(__METHOD__ . ':' . __LINE__);

        $theme = $this->app['types']->theme->fromForm([
            'theme_id'              => $request->request->get('theme_id'),
            'title'                 => $request->request->get('title'),
            'is_hidden'             => $request->request->get('is_hidden') === '1',
            'intro'                 => $request->request->get('intro'),
            'min_exercise_id'       => $request->request->get('min_exercise_id'),
            'max_exercise_id'       => $request->request->get('max_exercise_id'),
            'advanced_percent'      => $request->request->get('advanced_percent'),
            'intermediate_percent'  => $request->request->get('intermediate_percent'),
        ]);

        $this->log(__METHOD__ . ':' . __LINE__);

        if ($request->request->get('save')) {
            // Попросили сохранить тему.

            $this->log(__METHOD__ . ':' . __LINE__);

            // Проверить поля.
            // TODO: Прикрутить валидатор.
            if (! preg_match ('/\\d{1,2}/', $theme->theme_id)) {
                $errors[] = 'THEME_ID:NOT_A_NUMBER';
            } else {
                if (! ($theme->theme_id > 0)) {
                    $errors[] = 'THEME_ID:NOT_POSITIVE';
                } else {
                    // Если новый номер не равен старому, то проверить, что темы с новым номером ещё не существует.

                    $this->log(__METHOD__ . ':' . __LINE__ . ': $theme_id = ' . var_export($theme_id, true));
                    $this->log(__METHOD__ . ':' . __LINE__ . ': $theme->theme_id = ' . var_export($theme->theme_id, true));

                    if ($theme_id !== $theme->theme_id &&
                        $this->app['model']->theme->get($theme->theme_id)
                    ) {
                        $errors[] = 'THEME_ID:ALREADY_EXISTS';
                    }
                }
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            if (empty($theme->title)) {
                $errors[] = 'TITLE:EMPTY';
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            if (empty($theme->intro)) {
                $errors[] = 'INTRO:EMPTY';
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty($errors)) {

                $this->log(__METHOD__ . ':' . __LINE__ . ': count($errors) = ' . count($errors));
                $this->log(__METHOD__ . ':' . __LINE__ . ': $errors = ' . var_export($errors, true));

                return $this->themeRedirect($theme, $errors, $theme_id);
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            // Создать тему.
            $this->app['model']->theme->set($theme);

            $this->log(__METHOD__ . ':' . __LINE__);

            // Если старый номер не равен new, то после создания нового надо удалить старое.
            if ($theme_id !== 'new' && $theme_id !== $theme->theme_id) {
                $this->app['model']->theme->delete($theme_id);
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            // Показать новую тему в админке.
            return $this->app->redirect(
                $this->app['url_generator']->generate(
                    'admin_theme_view',
                    ['theme_id' => $theme->theme_id]
                )
            );
        } elseif ($request->request->get('delete')) {
            // Попросили удалить тему.

            $this->log(__METHOD__ . ':' . __LINE__);

            // Если есть ошибки, редиректнуть на форму и показать ошибки.
            if (! empty($errors)) {
                return $this->themeRedirect($theme, $errors, $theme_id);
            }

            $this->log(__METHOD__ . ':' . __LINE__);

            // Заранее посчитать, на какую страницу потом перенаправить пользователя.
            $page = $this->app['model']->theme->getPage($theme_id, self::PER_PAGE, true);

            $this->log(__METHOD__ . ':' . __LINE__);

            // Удалить задачу.
            $this->app['model']->theme->delete($theme_id);

            $this->log(__METHOD__ . ':' . __LINE__);

            // Показать список задач.
            return $this->app->redirect(
                $this->app['url_generator']->generate('admin_page', ['page' => $page])
            );
        }

        $this->log(__METHOD__ . ':' . __LINE__);
    }

    private function themeEdit(Request $request, $theme_id)
    {
        $this->log(__METHOD__ . ':' . __LINE__);

        $csrfKey = $this->themeGetCsrfToken($theme_id);

        $this->log(__METHOD__ . ':' . __LINE__);

        // Отобразить старую форму после редиректа.
        $formKey = $request->query->get('formKey');

        $this->log(__METHOD__ . ':' . __LINE__);

        if ($formKey) {

            $this->log(__METHOD__ . ':' . __LINE__);

            $data = $this->app['session']->get($formKey);

            $this->log(__METHOD__ . ':' . __LINE__ . ': data = ' . var_export($data, true));

            return $this->themeViewForm($data['theme'], $data['errors'], $theme_id, $csrfKey);
        }

        // Отобразить свежую форму для новой задачи.
        if ($theme_id === 'new') {
            return $this->themeViewForm(
                $this->app['types']->theme->fromNew($this->app['model']->theme->getNewId()),
                [],
                $theme_id,
                $csrfKey
            );
        }

        // Существует ли запрошенная задача?
        $theme = $this->app['model']->theme->get($theme_id);
        if (! $theme) {
            return $this->themeViewForm(null, ['THEME:DOES_NOT_EXIST'], $theme_id, $csrfKey);
        }

        // Отобразить свежую форму для старой задачи.
        return $this->themeViewForm($theme, [], $theme_id, $csrfKey);
    }

    private function themeViewForm($theme, $errors, $theme_id, $csrfKey)
    {
        $this->log(__METHOD__ . ':' . __LINE__);

        return $this->app->render('admin/theme/edit.twig', [
            'theme_id'  => $theme_id,
            'csrf'      => $this->app['csrf']->generate($csrfKey),
            'theme'     => $theme,
            'errors'    => $errors,
        ]);
    }

    private function themeGetCsrfToken($theme_id)
    {
        return 'admin_theme_edit_' . $theme_id;
    }

    private function themeRedirect($theme, $errors, $theme_id)
    {
        $this->log(__METHOD__ . ':' . __LINE__);

        $formKey = md5(microtime(true));

        $this->log(__METHOD__ . ':' . __LINE__);

        $this->app['session']->set($formKey, [
            'theme'  => $theme,
            'errors' => $errors,
        ]);

        $this->log(__METHOD__ . ':' . __LINE__);

        $url = $this->app['url_generator']->generate('admin_theme_edit', ['theme_id' => $theme_id]) .
            '?formKey=' . $formKey;

        $this->log(__METHOD__ . ':' . __LINE__);

        return $this->app->redirect($url);
    }

    private function log($message)
    {
        $this->app['monolog']->addInfo($message);
    }

    private function convertThemeId ($theme_id) {
        $theme_id = intval($theme_id);
        if ($theme_id < 1) {
            throw new Exception('Theme id must be positive integer');
        }
        return $theme_id;
    }
}
