<?php
namespace Zettai\Provider;

use Silex\Application,
    Silex\ServiceProviderInterface;
use Ob_Ivan\EviType\TypeService;
use Ob_Ivan\EviType\Sort\Sequence\Internal as SequenceInternal;

class TypeServiceProvider implements ServiceProviderInterface
{
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
            return $type
            ->view('english',  $type->select(['round' => 'english',   'square' => 'english']))
            ->view('e',        $type->select(['round' => 'e',         'square' => 'e'      ]))
            ->view('russian',  $type->select(['round' => 'russian',   'square' => 'russian']))
            ->view('r',        $type->select(['round' => 'r',         'square' => 'r'      ]))
            ->view('tenhou',   $type->select(['round' => 'tenhou',    'square' => 'tenhou' ]))
            ->export('tile', function (UnionInternal $internal) {
                return $internal->getValue()->toTile();
            });
        });
        $service->register('kyoku', function ($service) {
            $type = $service->product(
                $service['roundWind'],
                $service->enum(range(1, 4))
            );
            return $type
            ->view('english',  $type->separator('-',   ['english', 'default']))
            ->view('e',        $type->concat   (       ['e',       'default']))
            ->view('russian',  $type->separator('-',   ['russian', 'default']))
            ->view('r',        $type->concat   (       ['r',       'default']));
        });
        $service->register('abc', function ($service) {
            return $service->enum(['a', 'b', 'c']);
        });
        $service->register('suit', function ($service) {
            $type = $service->enum(['man', 'pin', 'sou']);
            return $type
            ->view('english',  $type->dictionary(['man', 'pin', 'sou']))
            ->view('e',        $type->dictionary(['m',   'p',   's'  ]))
            ->view('russian',  $type->dictionary(['ман', 'пин', 'со' ]))
            ->view('r',        $type->dictionary(['м',   'п',   'с'  ]));
        });
        $service->register('dragon', function ($service) {
            $type = $service->enum(['white', 'green', 'red']);
            return $type
            ->view('english',  $type->dictionary(['White', 'Green',   'Red'    ]))
            ->view('e',        $type->dictionary(['W',     'G',       'R'      ]))
            ->view('russian',  $type->dictionary(['Белый', 'Зелёный', 'Красный']))
            ->view('r',        $type->dictionary(['Б',     'З',       'К'      ]))
            ->view('tenhou',   $type->dictionary(['5z',    '6z',      '7z'     ]));
        });
        $service->register('tile', function ($service) {
            $number = $service->product(
                $service->enum([1, 2, 3, 4, 0, 5, 6, 7, 8, 9]),
                $service['suit']
            );
            $number
            ->view('english',  $number->separator(' ',   ['default', 'english']))
            ->view('e',        $number->concat   (       ['default', 'e',     ]))
            ->view('russian',  $number->separator(' ',   ['default', 'russian']))
            ->view('r',        $number->concat   (       ['default', 'r',     ]));

            $type = $service->union([
                'number' => $number,
                'wind'   => $service['wind'],
                'dragon' => $service['dragon'],
            ]);
            return $type
            ->view('english',   $type->select(['number' => 'english',   'wind' => 'english',    'dragon' => 'english'   ]))
            ->view('e',         $type->select(['number' => 'e',         'wind' => 'e',          'dragon' => 'e'         ]))
            ->view('russian',   $type->select(['number' => 'russian',   'wind' => 'russian',    'dragon' => 'russian'   ]))
            ->view('r',         $type->select(['number' => 'r',         'wind' => 'r',          'dragon' => 'r'         ]));
        });
        $service->register('tileSequence', function ($service) {
            $type = $service->sequence($service['tile']);
            $export = function ($view, $internal) {
                $isLong = in_array($view, ['english', 'russian']);
                $prevRankPresentation = null;
                $prevSuitPresentation = null;
                $presentations = [];
                foreach ($internal as $index => $value) {
                    $newPresentation = $value->to($view);
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
                        $presentation = substr($presentation, strlen($candidate->to($view)));
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
            $type = $service->record([
                'discard' => $service['tile'],
                'comment' => $service['string'],
            ]);
            return $type
            ->view('json', $type->json([
                'discard' => 'e',
                'comment' => 'string',
            ]));
        });
        $service->register('answerCollection', function ($service) {
            $type = $service->map($service['abc'], $service['answer']);
            return $type
            ->view('json', $type->json('default', 'json'));
        });
        $service->register('exerciseContent', function ($service) {
            $type = $service->record([
                'kyoku'         => $service['kyoku'],
                'position'      => $service['wind'],
                'turn'          => $service->enum(range(1, 18)),
                'dora'          => $service['tile'],
                'score'         => $service['string'],
                'hand'          => $service['tileSequence'],
                'draw'          => $service['tile'],
                'is_answered'   => $service['boolean'],
                'answer'        => $service['answerCollection'],
                'best_answer'   => $service['abc'],
            ]);
            $type->view('json', $type->json([
                'kyoku'         => 'e',
                'position'      => 'e',
                'turn'          => 'default',
                'dora'          => 'e',
                'score'         => 'string',
                'hand'          => 'e',
                'draw'          => 'e',
                'is_answered'   => 'integer',
                'answer'        => 'json',
                'best_answer'   => 'default',
            ]));
            return $type;
        });
        $service->register('exercise', function ($service) {
            $type = $service->record([
                'exercise_id'   => $service['integer'],
                'title'         => $service['string'],
                'is_hidden'     => $service['boolean'],
                'content'       => $service['exerciseContent'],
            ]);
            $type->view('database', $type->associative([
                'exercise_id'   => 'string',
                'title'         => 'string',
                'is_hidden'     => ['*', 'string'],
                'content'       => 'json',
            ]));
            // TODO: A shorter syntax.
            $type->getter('exercise_id',    function ($internal) { return $internal->exercise_id    ->toInteger(); });
            $type->getter('title',          function ($internal) { return $internal->title          ->toString (); });
            $type->getter('is_hidden',      function ($internal) { return $internal->is_hidden      ->toBoolean(); });
            $type->getter('content',        function ($internal) { return $internal->content                     ; });
            return $type;
        });
        $service->register('theme', function ($service) {
            $type = $service->record([
                'theme_id'              => $service['integer'],
                'title'                 => $service['string'],
                'is_hidden'             => $service['boolean'],
                'intro'                 => $service['string'],
                'min_exercise_id'       => $service['integer'],
                'max_exercise_id'       => $service['integer'],
                'advanced_percent'      => $service['integer'],
                'intermediate_percent'  => $service['integer'],
            ]);
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
            // TODO: A shorter syntax.
            $type->getter('theme_id',               function ($internal) { return $internal->theme_id               ->toInteger(); });
            $type->getter('title',                  function ($internal) { return $internal->title                  ->toString (); });
            $type->getter('is_hidden',              function ($internal) { return $internal->is_hidden              ->toBoolean(); });
            $type->getter('intro',                  function ($internal) { return $internal->intro                  ->toString (); });
            $type->getter('min_exercise_id',        function ($internal) { return $internal->min_exercise_id        ->toInteger(); });
            $type->getter('max_exercise_id',        function ($internal) { return $internal->max_exercise_id        ->toInteger(); });
            $type->getter('advanced_percent',       function ($internal) { return $internal->advanced_percent       ->toInteger(); });
            $type->getter('intermediate_percent',   function ($internal) { return $internal->intermediate_percent   ->toInteger(); });
            return $type;
        });
    }
}
