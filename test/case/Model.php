<?php
use Zettai\Application;
use Zettai\Config;

class ModelTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $app = new Application(new Config(DOCUMENT_ROOT));
    }
    
    public function test()
    {
        // TODO
    }
}
