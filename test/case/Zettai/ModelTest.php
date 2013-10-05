<?php
use Zettai\Application;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        // TODO: Eliminate code reduplication with Zettai\Entity\*Test.
        $this->app = new Application(DOCUMENT_ROOT);
        $app['model.prefix'] = 'test_';
        $this->app->boot();
    }

    public function test()
    {
        $exercise = $this->app['model']->exercise;
        $this->assertTrue($exercise instanceof Zettai\Entity\Exercise, 'Exercise entity has wrong type');

        $theme = $this->app['model']->theme;
        $this->assertTrue($theme instanceof Zettai\Entity\Theme, 'Theme entity has wrong type');
    }
}
