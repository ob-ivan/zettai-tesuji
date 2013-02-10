<?php
// Надо писать тесты для PHPUnit.

class ModelTestCase
{
    public function MondaiTest($app)
    {
        $mondai = ['mondai_id' => rand(200, 400), 'title' => 'a', 'content' => 'b'];
        print '<pre>setMondai</pre>';
        $app['model']->setMondai($mondai);
        print '<pre>getMondaiList = ' . print_r ($app['model']->getMondaiList(), true) . '</pre>';
        print '<pre>getMondaiCount = ' . print_r ($app['model']->getMondaiCount(), true) . '</pre>';
        print '<pre>getMondai(' . $mondai['mondai_id'] . ') = ' . print_r ($app['model']->getMondai($mondai['mondai_id']), true) . '</pre>';
        print '<pre>deleteMondai</pre>';
        $app['model']->deleteMondai($mondai['mondai_id']);
        print '<pre>getMondaiList = ' . print_r ($app['model']->getMondaiList(), true) . '</pre>';
        print '<pre>getMondaiCount = ' . print_r ($app['model']->getMondaiCount(), true) . '</pre>';
        print '<pre>getMondai(' . $mondai['mondai_id'] . ') = ' . print_r ($app['model']->getMondai($mondai['mondai_id']), true) . '</pre>';
    }
}
