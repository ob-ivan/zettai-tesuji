<?php
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

    public function testGetNewId_basic()
    {
        $newId = $this->exerciseEntity->getNewId();
        $this->assertGreaterThan(0, $newId, 'New id for exercise is not positive');

        $null = $this->exerciseEntity->get($newId);
        $this->assertEmpty($null, 'New id corresponds to an existing exercise');
    }

    public function testSetGetDelete()
    {
        $exercise = $this->generateExercise();

        $this->exerciseEntity->set($exercise);
        $exercise2 = $this->exerciseEntity->get($exercise->exercise_id);
        $this->assertEquals($exercise, $exercise2, 'Exercise::get returns wrong value');

        $this->exerciseEntity->delete($exercise->exercise_id);
        $exercise3 = $this->exerciseEntity->get($exercise->exercise_id);
        $this->assertEmpty($exercise3, 'Test exercise is not deleted');
    }

    // private //

    private function generateExercise()
    {
        $newExerciseId  = $this->exerciseEntity->getNewId() + mt_rand(10, 100);
        $newTitle       = $this->generateText(30);
        $newIsHidden    = ! mt_rand(0, 1);
        $newKyoku       = $this->types['kyoku']->random();
        $newPosition    = $this->types['wind']->random();
        $newTurn        = mt_rand(1, 18);
        $newDora        = $this->types['tile']->random();
        $newScore       = $this->generateText(30);
        $newHand        = $this->types['tileSequence']->fromArray($this->generateTiles(13));
        $newDraw        = $this->types['tile']->random();
        $newIsAnswered  = ! mt_rand(0, 1);
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

        $exercise = $this->exerciseType->fromArray([
            'exercise_id'   => $newExerciseId,
            'title'         => $newTitle,
            'is_hidden'     => $newIsHidden,
            'content'       => $newContent,
        ]);

        $this->assertTrue($exercise instanceof Value, 'Generated exercise must be an instance of Value');

        return $exercise;
    }

    private function generateTiles($count)
    {
        $tiles = [];
        for ($i = 0; $i < $count; ++$i) {
            $tiles[] = $this->types['tile']->random();
        }

        foreach ($tiles as $tile) {
            $this->assertTrue($this->types['tile']->has($tile), 'Tile does not belong to type "tile"');
        }

        return $tiles;
    }

    private function generateAnswers()
    {
        $pairs = [];
        foreach ($this->types['abc'] as $letter) {
            $pairs[] = [$letter, $this->generateAnswer()];
        }
        return $this->types['answerCollection']->fromPairs($pairs);
    }
}
