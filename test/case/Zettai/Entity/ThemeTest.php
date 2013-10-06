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
        // TODO: Eliminate code reduplication with ExerciseTest.
        $app = new Application(DOCUMENT_ROOT);
        $app['model.prefix'] = 'test_';
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
        for ($i = 0; $i < 2; ++$i) {
            $theme = $this->generateTheme([
                'isHidden' => $i % 2,
            ]);

            $this->themeEntity->set($theme);
            $theme2 = $this->themeEntity->get($theme->theme_id);
            $this->assertEquals($theme, $theme2, 'Theme::get returns wrong value');

            $this->themeEntity->delete($theme->theme_id);
            $theme3 = $this->themeEntity->get($theme->theme_id);
            $this->assertEmpty($theme3, 'Test theme is not deleted');
        }
    }

    public function testGetCount()
    {
        $countAll     = $this->themeEntity->getCount(true);
        $countVisible = $this->themeEntity->getCount(false);

        // Add hidden theme.
        $hiddenTheme = $this->generateTheme(['isHidden' => true]);
        $this->themeEntity->set($hiddenTheme);
        $this->assertEquals(
            1 + $countAll,
            $this->themeEntity->getCount(true),
            'Total count did not increase after a hidden theme was added'
        );
        $this->assertEquals(
            $countVisible,
            $this->themeEntity->getCount(false),
            'Visible count increased after a hidden theme was added'
        );

        // Add visible theme.
        $visibleTheme = $this->generateTheme(['isHidden' => false]);
        $this->themeEntity->set($visibleTheme);
        $this->assertEquals(
            2 + $countAll,
            $this->themeEntity->getCount(true),
            'Total count did not increase after a visible theme was added'
        );
        $this->assertEquals(
            1 + $countVisible,
            $this->themeEntity->getCount(false),
            'Visible count did not increase after a visible theme was added'
        );

        // Cleanup.
        $this->themeEntity->delete($hiddenTheme->theme_id);
        $this->themeEntity->delete($visibleTheme->theme_id);

        $this->assertEquals(
            $countAll,
            $this->themeEntity->getCount(true),
            'Total count did not restore after an added hidden theme was deleted'
        );
        $this->assertEquals(
            $countVisible,
            $this->themeEntity->getCount(false),
            'Visible count did not restore after an added visible theme was deleted'
        );
    }

    public function testTruncate()
    {
        $this->themeEntity->truncate();
        $this->assertEquals(0, $this->themeEntity->getCount(true), 'Table is not empty after truncate');
    }

    /**
     * Test getList method.
     *
     * [int => Value] getList(int $offset, int $limit, boolean $includeHidden = false)
    **/
    public function testGetList()
    {
        // 0. Work with empty tables.
        $this->themeEntity->truncate();

        // 1. Setup database.
        $themeHidden    = $this->generateTheme(['isHidden' => true]);
        $this->themeEntity->set($themeHidden);

        $themeNotHidden = $this->generateTheme(['isHidden' => false]);
        $this->themeEntity->set($themeNotHidden);

        $randomHidden   = ! mt_rand(0, 1);
        $themeRandom    = $this->generateTheme(['isHidden' => $randomHidden]);
        $this->themeEntity->set($themeRandom);
        $arrayRandom = $randomHidden ? [] : [$themeRandom];

        // 2. Run a series of getList's.
        foreach ([
            ['offset' => 0, 'limit' => 10, 'includeHidden' => true,  'expect' => [$themeHidden, $themeNotHidden, $themeRandom]],
            ['offset' => 0, 'limit' => 10, 'includeHidden' => false, 'expect' => array_merge([$themeNotHidden], $arrayRandom)],
            ['offset' => 1, 'limit' => 10, 'includeHidden' => true,  'expect' => [$themeNotHidden, $themeRandom]],
            ['offset' => 1, 'limit' => 10, 'includeHidden' => false, 'expect' => $arrayRandom],
            ['offset' => 0, 'limit' =>  1, 'includeHidden' => true,  'expect' => [$themeHidden]],
            ['offset' => 0, 'limit' =>  1, 'includeHidden' => false, 'expect' => [$themeNotHidden]],
            ['offset' => 1, 'limit' =>  1, 'includeHidden' => true,  'expect' => [$themeNotHidden]],
            ['offset' => 1, 'limit' =>  1, 'includeHidden' => false, 'expect' => $arrayRandom],
        ] as $data) {
            $this->assertEquals(
                $data['expect'],
                $this->themeEntity->getList($data['offset'], $data['limit'], $data['includeHidden'])
            );
        }

        // 3. Cleanup.
        $this->themeEntity->delete($themeHidden->theme_id);
        $this->themeEntity->delete($themeNotHidden->theme_id);
        $this->themeEntity->delete($themeRandom->theme_id);
    }

    // TODO: testGetNextId, testGetPrevId

    // private //

    /**
     * Generates a theme with given parameters.
     *
     *  @param  array   $parameters
     *      [
     *          'isHidden'  => <boolean>,
     *      ]
     *  @return Value   Belongs to $this->themeType.
    **/
    private function generateTheme(array $parameters = [])
    {
        $isHidden = isset($parameters['isHidden'])
            ? !! $parameters['isHidden']
            : ! mt_rand(0, 1);

        $lastExerciseId         = 100;
        $min_exercise_id        = mt_rand(1, $lastExerciseId);
        $max_exercise_id        = mt_rand($min_exercise_id, $lastExerciseId);
        $advanced_percent       = mt_rand(0, 100);
        $intermediate_percent   = mt_rand(0, $advanced_percent);
        $newThemeId             = $this->themeEntity->getNewId() + mt_rand(0, 100);
        $newTitle               = $this->generateText(20);
        $newIsHidden            = $isHidden;
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
