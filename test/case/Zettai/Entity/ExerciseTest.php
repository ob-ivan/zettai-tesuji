<?php
use Ob_Ivan\EviType\Value;
use Ob_Ivan\TestCase\AbstractCase;
use Zettai\Application;

class ExerciseTest extends AbstractCase
{
    public function setUp()
    {
        $app = new Application(DOCUMENT_ROOT);
        $app->boot();

        $this->exerciseEntity   = $app['model']->exercise;
        $this->exerciseType     = $app['types']->exercise;
        $this->types            = $app['types'];
    }

    public function testGetNewId()
    {
        $newId = $this->exerciseEntity->getNewId();
        $this->assertGreaterThan(0, $newId, 'New id for exercise is not positive');

        $null = $this->exerciseEntity->get($newId);
        $this->assertEmpty($null, 'New id corresponds to an existing exercise');
    }

    public function testSetGetDelete()
    {
        for ($i = 0; $i < 4; ++$i) {
            $exercise = $this->generateExercise([
                'isHidden'   => $i % 2,
                'isAnswered' => $i > 1,
            ]);

            $this->exerciseEntity->set($exercise);
            $exercise2 = $this->exerciseEntity->get($exercise->exercise_id);
            $this->assertEquals($exercise, $exercise2, 'Exercise::get returns wrong value');

            $this->exerciseEntity->delete($exercise->exercise_id);
            $exercise3 = $this->exerciseEntity->get($exercise->exercise_id);
            $this->assertEmpty($exercise3, 'Test exercise is not deleted');
        }
    }

    public function testUpdate()
    {
        for ($i = 0; $i < 4; ++$i) {
            $first = $this->generateExercise([
                'isHidden'   => $i % 2,
                'isAnswered' => $i > 1,
            ]);

            $this->exerciseEntity->set($first);

            for ($j = 0; $j < 4; ++$j) {
                $second = $this->generateExercise([
                    'exerciseId' => $first->exercise_id,
                    'isHidden'   => $i % 2,
                    'isAnswered' => $i > 1,
                ]);
                $this->exerciseEntity->set($second);

                $third = $this->exerciseEntity->get($first->exercise_id);
                $this->assertEquals($second, $third, 'Retrieved exercise is not the same as stored one');
            }

            $this->exerciseEntity->delete($first->exercise_id);
        }
    }

    // private //

    private function generateAnswer()
    {
        return $this->types['answer']->fromArray([
            'discard' => $this->generateTile(),
            'comment' => $this->generateText(100),
        ]);
    }

    private function generateAnswers()
    {
        $array = [];
        foreach ($this->types['abc'] as $letter) {
            $array[$letter->toDefault()] = $this->generateAnswer();
        }
        return $this->types['answerCollection']->fromArray($array);
    }

    /**
     * Generates a random exercise based on input parameters.
     *
     *  @param  array   $parameters
     *      Predefined values if needed.
     *      Each parameter is optional and is filled with a random value if omitted.
     *      [
     *          'exerciseId'    => <integer>,
     *          'isHidden'      => <boolean>,
     *          'isAnswered'    => <boolean>,
     *      ]
     *
     *  @return Value   Belongs to $this->exerciseType.
    **/
    private function generateExercise(array $parameters = [])
    {
        $newExerciseId  = isset($parameters['exerciseId'])
            ? $parameters['exerciseId']
            : $this->exerciseEntity->getNewId() + mt_rand(10, 100);

        $newIsHidden    = isset($parameters['isHidden'])
            ? !! $parameters['isHidden']
            : ! mt_rand(0, 1);

        $isAnswered    = isset($parameters['isAnswered'])
            ? !! $parameters['isAnswered']
            : ! mt_rand(0, 1);

        $newTitle       = $this->generateText(30);

        // content //

        $newKyoku       = $this->types['kyoku']->random();
        $newPosition    = $this->types['wind']->random();
        $newTurn        = $this->types['turnNumber']->random();
        $newDora        = $this->types['tile']->random();
        $newScore       = $this->generateText(30);
        $newHand        = $this->types['tileSequence']->fromArray($this->generateTiles(13));
        $newDraw        = $this->types['tile']->random();
        $newIsAnswered  = $this->types['boolean']->fromBoolean($isAnswered);
        $newAnswer      = $this->generateAnswers();
        $newBestAnswer  = $this->types['abc']->random();

        $this->assertTrue($this->types['wind']->has($newPosition), 'Position does not belong to type "wind"');
        $this->assertTrue($this->types['tileSequence']->has($newHand), 'Hand does not belong to type "tileSequence"');

        $newContent     = $this->types['exerciseContent']->fromArray([
            'kyoku'         => $newKyoku,
            'position'      => $newPosition,
            'turn'          => $newTurn,
            'dora'          => $newDora,
            'score'         => $newScore,
            'hand'          => $newHand,
            'draw'          => $newDraw,
            'is_answered'   => $newIsAnswered,
            'answer'        => $newAnswer,
            'best_answer'   => $newBestAnswer,
        ]);

        $this->assertTrue(
            $newContent instanceof Value,
            'Generated content must be an instance of Value, ' .
            get_class($newContent) . ' given'
        );
        $this->assertTrue($this->types['exerciseContent']->has($newContent), 'Exercise content must belong to exerciseContent type');

        $this->assertEquals($newKyoku,      $newContent->kyoku,         'Kyoku is not recognized properly');
        $this->assertEquals($newPosition,   $newContent->position,      'Position is not recognized properly');
        $this->assertEquals($newTurn,       $newContent->turn,          'Turn is not recognized properly');
        $this->assertEquals($newDora,       $newContent->dora,          'Dora is not recognized properly');
        $this->assertEquals($newScore,      $newContent->score,         'Score is not recognized properly');
        $this->assertEquals($newHand,       $newContent->hand,          'Hand is not recognized properly');
        $this->assertEquals($newDraw,       $newContent->draw,          'Draw is not recognized properly');
        $this->assertEquals($newIsAnswered, $newContent->is_answered,   'IsAnswered is not recognized properly');
        $this->assertEquals($newAnswer,     $newContent->answer,        'Answer is not recognized properly');
        $this->assertEquals($newBestAnswer, $newContent->best_answer,   'BestAnswer is not recognized properly');

        // exercise //

        $exercise = $this->exerciseType->fromArray([
            'exercise_id'   => $newExerciseId,
            'title'         => $newTitle,
            'is_hidden'     => $newIsHidden,
            'content'       => $newContent,
        ]);

        $this->assertTrue($exercise instanceof Value, 'Exercise must be an instance of Value');
        $this->assertTrue($this->exerciseType->has($exercise), 'Exercise must belong to exercise type');

        $this->assertEquals($newExerciseId, $exercise->exercise_id, 'Exercise id is not recognized properly');
        $this->assertEquals($newTitle,      $exercise->title,       'Exercise title is not recognized properly');
        $this->assertEquals($newIsHidden,   $exercise->is_hidden,   'is_hidden is not recognized properly');
        $this->assertEquals($newContent,    $exercise->content,     'Exercise content is not recognized properly');

        $this->assertEquals($newExerciseId, $exercise['exercise_id'], 'Exercise id is not recognized properly');
        $this->assertEquals($newTitle,      $exercise['title'],       'Exercise title is not recognized properly');
        $this->assertEquals($newIsHidden,   $exercise['is_hidden'],   'is_hidden is not recognized properly');
        $this->assertEquals($newContent,    $exercise['content'],     'Exercise content is not recognized properly');

        return $exercise;
    }

    private function generateTile()
    {
        return $this->types['tile']->random();
    }

    private function generateTiles($count)
    {
        $tiles = [];
        for ($i = 0; $i < $count; ++$i) {
            $tiles[] = $this->generateTile();
        }

        foreach ($tiles as $tile) {
            $this->assertTrue($this->types['tile']->has($tile), 'Tile does not belong to type "tile"');
        }

        return $tiles;
    }
}
