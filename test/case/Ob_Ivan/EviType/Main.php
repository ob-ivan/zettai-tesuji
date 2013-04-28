<?php

use Ob_Ivan\EviType\TypeService;

class Main extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = new TypeService();
    }

    public function testClass()
    {
        $this->assertTrue($this->service instanceof TypeService, 'Объект сервиса не принадлежит классу сервиса');
    }

    public function testArrayAccess()
    {
        // TODO
    }

    public function testRegister()
    {
        // TODO
    }
}
