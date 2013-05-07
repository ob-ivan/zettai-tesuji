<?php
use Monolog\Logger;
use Ob_Ivan\EviType\Value;
use Ob_Ivan\TestCase\AbstractCase;
use Silex\Provider\MonologServiceProvider;
use Zettai\Application;

class ThemeTestCase extends AbstractCase
{
    private $exerciseEntity;
    private $themeEntity;
    private $themeType;

    public function setUp()
    {
        $app = new Application(DOCUMENT_ROOT);
        $app->boot();

        $this->exerciseEntity   = $app['model']->exercise;
        $this->themeEntity      = $app['model']->theme;
        $this->themeType        = $app['types']->theme;
    }

    public function testGetNewId()
    {
        $newId = $this->themeEntity->getNewId();
        $this->assertGreaterThan(0, $newId, 'New id for theme is not positive');

        $null = $this->themeEntity->get($newId);
        $this->assertEmpty($null, 'New id corresponds to an existing theme');
    }

    public function testGenerate()
    {
        $theme = $this->generateTheme();
        $this->assertTrue($theme instanceof Value,          'Generated theme must be an instance of Value');
        $this->assertTrue($this->themeType->has($theme),    'Generated theme does not belong to its type');
        $this->assertGreaterThan(0, $theme['theme_id'],     'Generated theme has empty id');
    }

    public function testSetGetDelete()
    {
        $theme = $this->generateTheme();

        $this->themeEntity->set($theme);
        $theme2 = $this->themeEntity->get($theme->theme_id);
        $this->assertEquals($theme, $theme2, 'Theme::get returns wrong value');

        $this->themeEntity->delete($theme->theme_id);
        $theme3 = $this->themeEntity->get($theme->theme_id);
        $this->assertEmpty($theme3, 'Test theme is not deleted');
    }

    // private //

    private function generateTheme()
    {
        $lastExerciseId         = $this->exerciseEntity->getNewId() - 1;
        $min_exercise_id        = mt_rand(1, $lastExerciseId);
        $max_exercise_id        = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent       = mt_rand(0, 100);
        $intermediate_percent   = mt_rand(0, $advanced_percent);
        $newThemeId             = $this->themeEntity->getNewId();

        return $this->themeType->fromDatabase([
            'theme_id'              => $newThemeId + mt_rand(0, 100),
            'title'                 => $this->generateText(20),
            'is_hidden'             => mt_rand(0, 1),
            'intro'                 => $this->generateText(200),
            'min_exercise_id'       => $min_exercise_id,
            'max_exercise_id'       => $max_exercise_id,
            'advanced_percent'      => $advanced_percent,
            'intermediate_percent'  => $intermediate_percent,
        ]);
    }
}
