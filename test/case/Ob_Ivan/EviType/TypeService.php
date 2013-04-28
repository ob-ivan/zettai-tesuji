<?php

use Ob_Ivan\EviType\TypeService,
    Ob_Ivan\EviType\Value;
use Ob_Ivan\EviType\Type\Boolean\Type as BooleanType;
use Ob_Ivan\EviType\Type\Integer\Type as IntegerType;
use Ob_Ivan\EviType\Type\Product\Internal as ProductInternal;

class TypeService extends PHPUnit_Framework_TestCase
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

    public function testBoolean()
    {
        $type = $this->service['boolean'];
        $this->assertTrue($type instanceof BooleanType, 'Булевый тип не принадлежит своему типу');

        // true //

        $trueFromBoolean = $type->fromBoolean(true);
        $this->assertTrue($trueFromBoolean instanceof Value, 'Булевый тип не построил значение из истины');

        $trueFromInteger = $type->fromInteger(1);
        $this->assertTrue($trueFromInteger instanceof Value, 'Булевый тип не построил значение из единицы');

        $trueFromString = $type->fromString('true');
        $this->assertTrue($trueFromString instanceof Value, 'Булевый тип не построил значение из строки "true"');

        $this->assertTrue($trueFromBoolean === $trueFromInteger, 'Значения булевого типа, построенные из истины и единицы, различаются');
        $this->assertTrue($trueFromInteger === $trueFromString,  'Значения булевого типа, построенные из единицы и строки "true", различаются');

        // false //

        $falseFromBoolean = $type->fromBoolean(false);
        $this->assertTrue($falseFromBoolean instanceof Value, 'Булевый тип не построил значение из лжи');

        $falseFromInteger = $type->fromInteger(0);
        $this->assertTrue($falseFromInteger instanceof Value, 'Булевый тип не построил значение из нуля');

        $falseFromString = $type->fromString('false');
        $this->assertTrue($falseFromString instanceof Value, 'Булевый тип не построил значение из строки "false"');

        $this->assertTrue($falseFromBoolean === $falseFromInteger, 'Значения булевого типа, построенные из лжи и нуля, различаются');
        $this->assertTrue($falseFromInteger === $falseFromString,  'Значения булевого типа, построенные из нуля и строки "false", различаются');

        // true != false //

        $this->assertTrue($trueFromBoolean != $falseFromBoolean, 'Значения булевого типа для истины и лжи совпадают');
    }

    public function testInteger()
    {
        $type = $this->service['integer'];
        $this->assertTrue($type instanceof IntegerType, 'Целочисленный тип не принадлежит своему типу');

        $unitFromInteger = $type->fromInteger(1);
        $this->assertTrue($unitFromInteger instanceof Value, 'Целочисленный тип не построил значение из единицы');

        $unitFromString = $type->fromString('1');
        $this->assertTrue($unitFromString instanceof Value, 'Целочисленный тип не построил значение из строки "1"');

        $this->assertTrue(
            $unitFromInteger === $unitFromString,
            'Значения целого типа для единицы, построенные из разных представлений, различаются'
        );

        $two = $type->fromInteger(2);
        $this->assertTrue($two instanceof Value, 'Целочисленный тип не построил значение из двойки');

        $this->assertTrue($unitFromInteger != $two, 'Значения целого типа для единицы и двойки совпадают');
    }

    public function testProductStringInteger()
    {
        // set up //

        $type = $service->product($service['string'], $service['integer']);
        $this->assertTrue($type instanceof ProductType, 'Декартово произведение типов не принадлежит своему типу');

        $type->export('concat', function (ProductInternal $internal) {
            return $internal[0] . $internal[1];
        });
        $type->import('concat', function ($presentation) {
            if (preg_match('~^(.*?)(\d+)$~', $presentation, $matches)) {
                return new Internal([
                    trim($matches[0]),
                    intval($matches[1])
                ]);
            }
        });

        // test //

        $alice1value = $type->fromAny('alice 1');
        $this->assertTrue($alice1value instanceof Value, 'StringInteger не построил значение из строки "alice 1"');

        $alice1export = $alice1value->toConcat();
        $this->assertEquals('alice1', $alice1export, 'Неправильно экспортировалось значение из строки "alice 1"');
    }
}
