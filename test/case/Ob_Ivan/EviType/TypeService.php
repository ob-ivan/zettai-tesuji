<?php

use Ob_Ivan\EviType\TypeService,
    Ob_Ivan\EviType\Value;
use Ob_Ivan\EviType\Type\Boolean\Type as BooleanType;
use Ob_Ivan\EviType\Type\Integer\Type as IntegerType;
use Ob_Ivan\EviType\Type\Product\Internal as ProductInternal,
    Ob_Ivan\EviType\Type\Product\Type     as ProductType;
use Ob_Ivan\TestCase\AbstractCase;

class TypeServiceTest extends AbstractCase
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

        $type = $this->service->product($this->service['string'], $this->service['integer']);
        $this->assertTrue($type instanceof ProductType, 'Декартово произведение типов не принадлежит своему типу');

        $type->export('concat', function (ProductInternal $internal) {
            return $internal[0] . $internal[1];
        });
        $type->import('concat', function ($presentation, $options) {
            if (preg_match('~\d+$~', $presentation, $matches, PREG_OFFSET_CAPTURE)) {
                $substring = substr($presentation, 0, $matches[0][1]);
                return new ProductInternal([
                    $options[0]->fromString(trim($substring)),
                    $options[1]->fromString($matches[0][0]),
                ]);
            }
        });

        // test //

        $alice1value = $type->fromAny('alice 1');
        $this->assertTrue($alice1value instanceof Value, 'StringInteger не построил значение из строки "alice 1"');

        $alice1export = $alice1value->toConcat();
        $this->assertEquals('alice1', $alice1export, 'Неправильно экспортировалось значение из строки "alice 1"');
    }

    public function testRecordTheme()
    {
        // set up //

        $type = $this->service->record([
            'theme_id'              => $this->service['integer'],
            'title'                 => $this->service['string'],
            'is_hidden'             => $this->service['boolean'],
            'intro'                 => $this->service['string'],
            'min_exercise_id'       => $this->service['integer'],
            'max_exercise_id'       => $this->service['integer'],
            'advanced_percent'      => $this->service['integer'],
            'intermediate_percent'  => $this->service['integer'],
        ]);
        $this->assertTrue($type instanceof ProductType, 'Тип рекорда не принадлежит сорту произведений');

        $type->view('database', $type->associative([
            'theme_id'              => 'string',
            'title'                 => 'string',
            'is_hidden'             => ['*', 'string'],
            'intro'                 => 'string',
            'min_exercise_id'       => 'string',
            'max_exercise_id'       => 'string',
            'advanced_percent'      => 'string',
            'intermediate_percent'  => 'string',
        ]));

        // test //

        $lastExerciseId         = mt_rand(1, 100);
        $min_exercise_id        = mt_rand(1, $lastExerciseId);
        $max_exercise_id        = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent       = mt_rand(0, 100);
        $intermediate_percent   = mt_rand(0, $advanced_percent);

        $value = $type->fromDatabase([
            'theme_id'              => mt_rand(1, 100),
            'title'                 => $this->generateText(20),
            'is_hidden'             => mt_rand(0, 1),
            'intro'                 => $this->generateText(200),
            'min_exercise_id'       => $min_exercise_id,
            'max_exercise_id'       => $max_exercise_id,
            'advanced_percent'      => $advanced_percent,
            'intermediate_percent'  => $intermediate_percent,
        ]);
        $this->assertTrue($value instanceof Value, 'Generated value must be an instance of Value');
    }
}
