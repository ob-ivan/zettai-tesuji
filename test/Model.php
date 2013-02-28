<?php
// Надо писать тесты для PHPUnit.

class ModelTestCase
{
    public function ExerciseTest($app)
    {
        $exercise = ['exercise_id' => rand(200, 400), 'title' => 'a', 'content' => 'b'];
        print '<pre>setExercise</pre>';
        $app['model']->setExercise($exercise);
        print '<pre>getExerciseList = ' . print_r ($app['model']->getExerciseList(), true) . '</pre>';
        print '<pre>getExerciseCount = ' . print_r ($app['model']->getExerciseCount(), true) . '</pre>';
        print '<pre>getExercise(' . $exercise['exercise_id'] . ') = ' . print_r ($app['model']->getExercise($exercise['exercise_id']), true) . '</pre>';
        print '<pre>deleteExercise</pre>';
        $app['model']->deleteExercise($exercise['exercise_id']);
        print '<pre>getExerciseList = ' . print_r ($app['model']->getExerciseList(), true) . '</pre>';
        print '<pre>getExerciseCount = ' . print_r ($app['model']->getExerciseCount(), true) . '</pre>';
        print '<pre>getExercise(' . $exercise['exercise_id'] . ') = ' . print_r ($app['model']->getExercise($exercise['exercise_id']), true) . '</pre>';
    }
}
