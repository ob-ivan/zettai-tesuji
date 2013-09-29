<?php

use Ob_Ivan\EviType\TypeInterface;
use Ob_Ivan\EviType\Value;
use Ob_Ivan\TestCase\AbstractCase;
use Zettai\Application;
use Zettai\Provider\TypeServiceProvider;

class TypeService extends AbstractCase
{
    private $typeService;

    public function setUp()
    {
        $app = new Application(DOCUMENT_ROOT);
        $app->boot();

        $this->typeService = $app['types'];
    }

    public function testThemeType()
    {
        $type = $this->typeService['theme'];
        $this->assertTrue($type instanceof TypeInterface, 'Theme type is not a type');

        $lastExerciseId         = mt_rand(1, 100);
        $min_exercise_id        = mt_rand(1, $lastExerciseId);
        $max_exercise_id        = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent       = mt_rand(0, 100);
        $intermediate_percent   = mt_rand(0, $advanced_percent);

        foreach ([0, 1] as $isHidden) {
            $database = [
                'theme_id'              => mt_rand(1, 100),
                'title'                 => $this->generateText(20),
                'is_hidden'             => $isHidden,
                'intro'                 => $this->generateText(200),
                'min_exercise_id'       => $min_exercise_id,
                'max_exercise_id'       => $max_exercise_id,
                'advanced_percent'      => $advanced_percent,
                'intermediate_percent'  => $intermediate_percent,
            ];
            $value = $type->fromDatabase($database);
            $this->assertTrue($value instanceof Value, 'Generated value must be an instance of Value');

            $presentation = $value->toDatabase();
            $this->assertEquals($database, $presentation, 'Generated presentation differs from its origin');
        }
    }
}
