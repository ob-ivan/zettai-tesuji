<?php
use Zettai\Application;
use Zettai\Config;

class ThemeTestCase extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $entity;
    private $type;

    public function setUp()
    {
        $this->app = new Application(new Config(DOCUMENT_ROOT));
        $this->app->boot();

        $this->type     = $this->app['types']->theme;
        $this->entity   = $this->app['model']->theme;
    }

    public function testSetGetDelete()
    {
        $theme = $this->generateTheme();

        $this->entity->set($theme);
        $theme2 = $this->entity->get($theme->id);
        $this->assertEquals($theme, $theme2, 'Theme::get returns wrong value');

        $this->entity->delete($theme->id);
        $theme3 = $this->entity->get($theme->id);
        $this->assertEmpty($theme3, 'Test theme is not deleted');
    }

    // private //

    private function generateChar()
    {
        return chr(mt_rand(32, 126));
    }

    private function generateFloat($min, $max)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    private function generateText($maxLength)
    {
        $text = '';
        while (empty($text)) {
            $chars = [];
            for ($i = 0; $i < $maxLength; ++$i) {
                $chars[] = $this->generateChar();
            }
            $text = trim(implode('', $chars));
        }
        return $text;
    }

    private function generateTheme()
    {
        $lastExerciseId = $this->app['model']->exercise->getNewId() - 1;
        $min_exercise_id = mt_rand(1, $lastExerciseId);
        $max_exercise_id = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percentage = $this->generateFloat(0, 1);
        $intermediate_percentage = $this->generateFloat(0, $advanced_percentage);

        return $this->type->from([
            'theme_id'  => $this->entity->getNewId() + mt_rand(0, 100),
            'title'     => $this->generateText(20),
            'is_hidden' => mt_rand(0, 1),
            'intro'     => $this->generateText(200),
            'min_exercise_id' => $min_exercise_id,
            'max_exercise_id' => $max_exercise_id,
            'advanced_percentage' => $advanced_percentage,
            'intermediate_percentage' => $intermediate_percentage,
        ]);
    }
}
