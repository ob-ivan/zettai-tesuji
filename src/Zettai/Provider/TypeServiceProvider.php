<?php
namespace Zettai\Provider;

use Silex\Application,
    Silex\ServiceProviderInterface;
use Ob_Ivan\EviType\TypeService;
use Ob_Ivan\EviType\Type\Sequence\Internal as SequenceInternal;

class TypeServiceProvider implements ServiceProviderInterface
{
    // NEW

    public function register(Application $app)
    {
        $app['types'] = $app->share(function () {
            return new TypeService();
        });
    }

    public function boot(Application $app)
    {
        $service = $app['types'];

        // Регистрируем в порядке зависимости.
        // Первыми идут типы, не зависящие от других типов.

        $service->register('roundWind', function ($service) {
            $type = $service->enum(['east', 'south']);
            $type->view('english',  $type->dictionary(['east',      'south']));
            $type->view('e',        $type->dictionary(['e',         's']));
            $type->view('russian',  $type->dictionary(['восток',    'юг']));
            $type->view('r',        $type->dictionary(['в',         'ю']));

            $tenhouView = $type->dictionary(['1z', '2z']);
            $type->view('tenhou', $tenhouView);
            // TODO: Устранить копипасту с таким же экспортом в типе squareWind.
            $type->export('tile', function (EnumInternal $internal) use ($service, $tenhouView) {
                return $service['tile']->fromTenhou($tenhouView->export($internal));
            });

            return $type;
        });
        $service->register('squareWind', function ($service) {
            $type = $service->enum(['west', 'north']);
            $type->view('english', $type->dictionary(['west',   'north']));
            $type->view('e',       $type->dictionary(['w',      'n'    ]));
            $type->view('russian', $type->dictionary(['запад',  'север']));
            $type->view('r',       $type->dictionary(['з',      'с'    ]));

            $tenhouView = $type->dictionary(['3z', '4z']);
            $type->view('tenhou', $tenhouView);
            // TODO: Устранить копипасту с таким же экспортом в типе roundWind.
            $type->export('tile', function (EnumInternal $internal) use ($service, $tenhouView) {
                return $service['tile']->fromTenhou($tenhouView->export($internal));
            });

            return $type;
        });
        $service->register('wind', function ($service) {
            $type = $service->union([
                'round'  => $service['roundWind'],
                'square' => $service['squareWind'],
            ]);
            $type->view('english',  $type->select(['round' => 'english',   'square' => 'english']));
            $type->view('e',        $type->select(['round' => 'e',         'square' => 'e'      ]));
            $type->view('russian',  $type->select(['round' => 'russian',   'square' => 'russian']));
            $type->view('r',        $type->select(['round' => 'r',         'square' => 'r'      ]));
            $type->view('tenhou',   $type->select(['round' => 'tenhou',    'square' => 'tenhou' ]));

            $type->export('tile', function (UnionInternal $internal) {
                return $internal->getValue()->toTile();
            });

            return $type;
        });
        $service->register('kyoku', function ($service) {
            $type = $service->product(
                $service['roundWind'],
                $service->enum(range(1, 4))
            );
            $type->view('english',  $type->separator('-',   ['english', 'default']));
            $type->view('e',        $type->concat   (       ['e',       'default']));
            $type->view('russian',  $type->separator('-',   ['russian', 'default']));
            $type->view('r',        $type->concat   (       ['r',       'default']));
            return $type;
        });
        $service->register('abc', function ($service) {
            return $service->enum(['a', 'b', 'c']);
        });
        $service->register('suit', function ($service) {
            $type = $service->enum(['man', 'pin', 'so']);
            $type->view('english',  $type->dictionary(['man', 'pin', 'sou']));
            $type->view('e',        $type->dictionary(['m',   'p',   's'  ]));
            $type->view('russian',  $type->dictionary(['ман', 'пин', 'со' ]));
            $type->view('r',        $type->dictionary(['м',   'п',   'с'  ]));
            return $type;
        });
        $service->register('dragon', function ($service) {
            $type = $service->enum(['white', 'green', 'red']);
            $type->view('english',  $type->dictionary(['White', 'Green',   'Red'    ]));
            $type->view('e',        $type->dictionary(['W',     'G',       'R'      ]));
            $type->view('russian',  $type->dictionary(['Белый', 'Зелёный', 'Красный']));
            $type->view('r',        $type->dictionary(['Б',     'З',       'К'      ]));
            $type->view('tenhou',   $type->dictionary(['5z',    '6z',      '7z'     ]));
            return $type;
        });
        $service->register('tile', function ($service) {
            return $service->union([
                'number' => $service->product(
                    $service->enum([1, 2, 3, 4, 0, 5, 6, 7, 8, 9]),
                    $service['suit']
                ),
                'wind'   => $service['wind'],
                'dragin' => $service['dragon'],
            ]);
        });
        $service->register('tileSequence', function ($service) {
            $type = $service->sequence($service['tile']);
            $export = function ($view, $internal) {
                $isLong = in_array($view, ['english', 'russian']);
                $prevRankPresentation = null;
                $prevSuitPresentation = null;
                $presentations = [];
                foreach ($internal as $index => $value) {
                    $newPresentation = $value->toView($view);
                    if (preg_match('/^(\d)(\D+)$/', $newPresentation, $matches)) {
                        $newRankPresentation = $matches[1];
                        $newSuitPresentation = $matches[2];

                        if (! is_null($prevRankPresentation) && ! is_null($prevSuitPresentation)) {
                            $presentations[] = $prevRankPresentation;
                            if ($prevSuitPresentation !== $newSuitPresentation) {
                                $presentations[] = $prevSuitPresentation;
                                if ($isLong) {
                                    $presentations[] = ' ';
                                }
                            }
                        }
                        $prevRankPresentation = $newRankPresentation;
                        $prevSuitPresentation = $newSuitPresentation;
                    } else {
                        if (! is_null($prevRankPresentation) && ! is_null($prevSuitPresentation)) {
                            $presentations[] = $prevRankPresentation;
                            $presentations[] = $prevSuitPresentation;
                            if ($isLong) {
                                $presentations[] = ' ';
                            }
                        }
                        $prevRankPresentation = null;
                        $prevSuitPresentation = null;
                        $presentations[] = $newPresentation;
                        if ($isLong) {
                            $presentations[] = ' ';
                        }
                    }
                }
                if (! is_null($prevRankPresentation) && ! is_null($prevSuitPresentation)) {
                    $presentations[] = $prevRankPresentation;
                    $presentations[] = $prevSuitPresentation;
                }
                return implode('', $presentations);
            };
            $import = function ($view, $presentation) {
                $internal = [];
                while (! empty($presentation)) {
                    $prevLength = strlen($presentation);

                    $presentation = trim($presentation);
                    $candidate = $this->element->fromView($view, $presentation);
                    if ($candidate) {
                        $internal[] = $candidate;
                        $presentation = substr($presentation, strlen($candidate->toView($view)));
                    } elseif (preg_match('~^(\d+)([^\d\s]+)~', $presentation, $matches)) {
                        $ranks = $matches[1];
                        $suit  = $matches[2];
                        for ($i = 0, $l = strlen($ranks); $i < $l; ++$i) {
                            $candidate = $this->element->fromView($view, $ranks[$i] . $suit);
                            if ($candidate) {
                                $internal[] = $candidate;
                            }
                        }
                        $presentation = substr($presentation, strlen($matches[0]));
                    } else {
                        break;
                    }

                    if ($prevLength <= strlen($presentation)) {
                        break;
                    }
                }
                return $this->value($internal);
            };
            $type->export('english', function (SequenceInternal $internal) use ($export) { return $export('english', $internal); });
            $type->export('e',       function (SequenceInternal $internal) use ($export) { return $export('e',       $internal); });
            $type->export('russian', function (SequenceInternal $internal) use ($export) { return $export('russian', $internal); });
            $type->export('r',       function (SequenceInternal $internal) use ($export) { return $export('r',       $internal); });
            return $type;
        });
        $service->register('answer', function ($service) {
            return $service->record([
                'discard' => $service['tile'],
                'comment' => $service['string'],
            ]);
        });
        $service->register('exerciseContent', function ($service) {
            return $service->record([
                'kyoku'         => $service['kyoku'],
                'position'      => $service['wind'],
                'turn'          => range(1, 18),
                'dora'          => $service['tile'],
                'score'         => $service['string'],
                'hand'          => $service['tileSequence'],
                'draw'          => $service['tile'],
                'is_answered'   => $service['boolean'],
                'answer'        => $service->map(
                    $service['abc'],
                    $service['answer']
                ),
                'best_answer' => $service['abc'],
            ]);
        });
        $service->register('exercise', function ($service) {
            return $service->record([
                'exercise_id'   => $service['integer'],
                'title'         => $service['string'],
                'is_hidden'     => $service['boolean'],
                'content'       => $service['exerciseContent'],
            ]);
        });
    }

    // OLD

    public function _boot(Application $app)
    {
        $service = $app['types'];

        $service->register('theme', function ($service) {
            return $service->record([
                'theme_id'              => $service->integer(),
                'title'                 => $service->text(),
                'is_hidden'             => $service->boolean(),
                'intro'                 => $service->text(),
                'min_exercise_id'       => $service->integer(),
                'max_exercise_id'       => $service->integer(),
                'advanced_percent'      => $service->integer(),
                'intermediate_percent'  => $service->integer(),
            ]);
        });
    }
}
