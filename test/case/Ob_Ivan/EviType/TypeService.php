<?php

use Ob_Ivan\EviType\TypeService;
use Ob_Ivan\EviType\Type\Integer\Type as IntegerType;

class Main extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->service = new TypeService();
    }

    // test generic interface //

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

    // test standard types //

    public function testInteger()
    {
        $type = $this->service['integer'];
        $this->assertTrue($type instanceof IntegerType, 'Целочисленный тип не принадлежит своему типу');

        $unitFromInteger = $type->fromInteger(1);
        $this->assertTrue($unitFromInteger instanceof Value, 'Целочисленный тип не построил значение из единицы');

        $unitFromString = $type->fromString('1');
        $this->assertTrue($unitFromString instanceof Value, 'Целочисленный тип не построил значение из строки "1"');

        $this->assertTrue($unitFromInteger === $unitFromString, 'Значения для единицы, построенные из разных представлений, различаются');
    }
}
