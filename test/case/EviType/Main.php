<?php

use Ob_Ivan\EviType\Service;

class Main extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = new Service();
    }
    
    public function testClass()
    {
        $this->assertTrue($this->service instanceof Service, 'Объект сервиса не принадлежит классу сервиса');
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
