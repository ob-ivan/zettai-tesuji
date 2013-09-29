<?php
use Ob_Ivan\EviType\Value;
use Ob_Ivan\TestCase\AbstractCase;
use Zettai\Application;

class ThemeTest extends AbstractCase
{
    private $exerciseEntity;
    private $themeEntity;
    private $themeType;

    public function setUp()
    {
        $app = new Application(DOCUMENT_ROOT);

        // TODO: Substitute [db] with a mock in-memory database.

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

    // TODO: testGetList, testGetNextId, testGetPrevId

    // private //

    private function generateTheme()
    {
        $lastExerciseId         = $this->exerciseEntity->getNewId() - 1;
        $min_exercise_id        = mt_rand(1, $lastExerciseId);
        $max_exercise_id        = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent       = mt_rand(0, 100);
        $intermediate_percent   = mt_rand(0, $advanced_percent);
        $newThemeId             = $this->themeEntity->getNewId() + mt_rand(0, 100);
        $newTitle               = $this->generateText(20);
        $newIsHidden            = ! mt_rand(0, 1);
        $newIntro               = $this->generateText(200);

        $theme = $this->themeType->fromDatabase([
            'theme_id'              => $newThemeId,
            'title'                 => $newTitle,
            'is_hidden'             => $newIsHidden,
            'intro'                 => $newIntro,
            'min_exercise_id'       => $min_exercise_id,
            'max_exercise_id'       => $max_exercise_id,
            'advanced_percent'      => $advanced_percent,
            'intermediate_percent'  => $intermediate_percent,
        ]);

        $this->assertTrue($theme instanceof Value,           'Generated theme must be an instance of Value');
        $this->assertTrue($this->themeType->has($theme),     'Generated theme does not belong to its type');
        $this->assertEquals($newThemeId,  $theme->theme_id,  'Generated theme has invalid theme_id');
        $this->assertEquals($newTitle,    $theme->title,     'Generated theme has invalid title');
        $this->assertEquals($newIsHidden, $theme->is_hidden, 'Generated theme has invalid is_hidden');
        $this->assertEquals($newIntro,    $theme->intro,     'Generated theme has invalid intro');

        return $theme;
    }
}
