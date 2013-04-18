<?php
use Zettai\Application;
use Zettai\Config;

class ModelTestCase extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Application(new Config(DOCUMENT_ROOT));
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
