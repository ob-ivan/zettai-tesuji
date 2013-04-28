<?php

use Silex\Application;
use Zettai\Provider\TypeServiceProvider;

class TypeService extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $app = new Application;
        $app->register(function () { return new TypeServiceProvider(); });
    }
}
