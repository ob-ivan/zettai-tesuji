<?php
use Zettai\Application;
use Zettai\Config;

class ThemeTestCase extends \PHPUnit_Framework_TestCase
{
    private $exerciseEntity;
    private $themeEntity;
    private $themeType;

    public function setUp()
    {
        $app = new Application(new Config(DOCUMENT_ROOT));
        $app->boot();

        $this->exerciseEntity   = $app['model']->exercise;
        $this->themeEntity      = $app['model']->theme;
        $this->themeType        = $app['types']->theme;
    }

    public function testGenerate()
    {
        $theme = $this->generateTheme();
        $this->assertTrue($theme instanceof Zettai\Type\Value, 'Generated theme must be an instance of Value');
        $this->assertTrue($this->themeType->has($theme), 'Generated theme does not belong to its type');
    }

    public function testSetGetDelete()
    {
        $theme = $this->generateTheme();

        $this->themeEntity->set($theme);
        $theme2 = $this->themeEntity->get($theme->id);
        $this->assertEquals($theme, $theme2, 'Theme::get returns wrong value');

        $this->themeEntity->delete($theme->id);
        $theme3 = $this->themeEntity->get($theme->id);
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
        $lastExerciseId = $this->exerciseEntity->getNewId() - 1;
        $min_exercise_id = mt_rand(1, $lastExerciseId);
        $max_exercise_id = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent = mt_rand(0, 100);
        $intermediate_percent = mt_rand(0, $advanced_percent);

        return $this->themeType->fromArray([
            'theme_id'  => $this->themeEntity->getNewId() + mt_rand(0, 100),
            'title'     => $this->generateText(20),
            'is_hidden' => mt_rand(0, 1),
            'intro'     => $this->generateText(200),
            'min_exercise_id' => $min_exercise_id,
            'max_exercise_id' => $max_exercise_id,
            'advanced_percent' => $advanced_percent,
            'intermediate_percent' => $intermediate_percent,
        ]);
    }
}
