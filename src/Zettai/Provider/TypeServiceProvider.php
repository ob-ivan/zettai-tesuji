<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Ob_Ivan\EviType\TypeService;
use Ob_Ivan\EviType\Sort\Product\Internal as ProductInternal;
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
            ->view('english',  $type->separator('-',   ['english', 'integer']))
            ->view('e',        $type->concat   (       ['e',       'integer']))
            ->view('russian',  $type->separator('-',   ['russian', 'integer']))
            ->view('r',        $type->concat   (       ['r',       'integer']));
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
            ->view('english',  $number->separator(' ',   ['integer', 'english']))
            ->view('e',        $number->concat   (       ['integer', 'e',     ]))
            ->view('russian',  $number->separator(' ',   ['integer', 'russian']))
            ->view('r',        $number->concat   (       ['integer', 'r',     ]))
            ->view('tenhou',   $number->concat   (       ['integer', 'e'      ]));

            $type = $service->union([
                'number' => $number,
                'wind'   => $service['wind'],
                'dragon' => $service['dragon'],
            ]);
            return $type
            ->view('english',   $type->select(['number' => 'english',   'wind' => 'english',    'dragon' => 'english'   ]))
            ->view('e',         $type->select(['number' => 'e',         'wind' => 'e',          'dragon' => 'e'         ]))
            ->view('russian',   $type->select(['number' => 'russian',   'wind' => 'russian',    'dragon' => 'russian'   ]))
            ->view('r',         $type->select(['number' => 'r',         'wind' => 'r',          'dragon' => 'r'         ]))
            ->view('tenhou',    $type->select(['number' => 'tenhou',    'wind' => 'tenhou',     'dragon' => 'tenhou'    ]));
        });
        $service->register('tileSequence', function ($service) {
            $element = $service['tile'];
            $type = $service->sequence($element);
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
            $import = function ($view, $presentation) use ($element) {
                $array = [];
                while (! empty($presentation)) {
                    $prevLength = strlen($presentation);

                    $presentation = trim($presentation);
                    if (preg_match('~^(\d+)([^\d\s]+)~', $presentation, $matches)) {
                        $ranks = $matches[1];
                        $suit  = $matches[2];
                        for ($i = 0, $l = strlen($ranks); $i < $l; ++$i) {
                            $candidate = $element->from($view, $ranks[$i] . $suit);
                            if ($candidate) {
                                $array[] = $candidate;
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
                return new SequenceInternal($array);
            };
            $type->export('english', function (SequenceInternal $internal) use ($export) { return $export('english', $internal); });
            $type->export('e',       function (SequenceInternal $internal) use ($export) { return $export('e',       $internal); });
            $type->export('russian', function (SequenceInternal $internal) use ($export) { return $export('russian', $internal); });
            $type->export('r',       function (SequenceInternal $internal) use ($export) { return $export('r',       $internal); });
            $type->export('tenhou',  function (SequenceInternal $internal) use ($export) { return $export('tenhou',  $internal); });

            $type->import('tenhou',  function ($presentation) use ($import) { return $import('tenhou',  $presentation); });

            return $type;
        });
        $service->register('answer', function ($service) {
            $type = $service->record([
                'discard' => $service['tile'],
                'comment' => $service['string'],
            ]);
            return $type
            ->view('json', $type->json([
                'discard' => 'tenhou',
                'comment' => 'string',
            ]))
            ->import('dummy', function () use ($service) {
                return new ProductInternal([
                    'discard' => $service['tile']->fromTenhou('5z'),
                    'comment' => $service['string']->fromString(''),
                ]);
            });
        });
        $service->register('answerCollection', function ($service) {
            $type = $service->record([
                'a' => $service['answer'],
                'b' => $service['answer'],
                'c' => $service['answer'],
            ]);
            return $type
            ->view('json', $type->json([
                'a' => 'json',
                'b' => 'json',
                'c' => 'json',
            ]))
            ->import('new', function () use ($service) {
                $dummyAnswer = $service['answer']->fromDummy();
                return new ProductInternal([
                    'a' => $dummyAnswer,
                    'b' => $dummyAnswer,
                    'c' => $dummyAnswer,
                ]);
            });
        });
        $service->register('turnNumber', function ($service) {
            return $service->enum(range(1, 18));
        });
        $service->register('exerciseContent', function ($service) {
            $type = $service->record([
                'kyoku'         => $service['kyoku'],
                'position'      => $service['wind'],
                'turn'          => $service['turnNumber'],
                'dora'          => $service['tile'],
                'score'         => $service['string'],
                'hand'          => $service['tileSequence'],
                'draw'          => $service['tile'],
                'is_answered'   => $service['boolean'],
                'answer'        => $service['answerCollection'],
                'best_answer'   => $service['abc'],
            ]);
            $subviews = [
                'kyoku'         => ['*', 'e'],
                'position'      => ['*', 'e'],
                'turn'          => ['*', 'default'],
                'dora'          => 'tenhou',
                'score'         => 'string',
                'hand'          => 'tenhou',
                'draw'          => 'tenhou',
                'is_answered'   => 'integer',
                'answer'        => 'json',
                'best_answer'   => 'default',
            ];
            $type->view('form', $type->associative($subviews));
            $type->view('json', $type->json($subviews));
            $type->import('new', function () use ($service) {
                return new ProductInternal([
                    'kyoku'         => $service['kyoku']            ->fromE('e1'),
                    'position'      => $service['wind']             ->fromE('e'),
                    'turn'          => $service['turnNumber']       ->fromInteger(1),
                    'dora'          => $service['tile']             ->fromTenhou('5z'),
                    'score'         => $service['string']           ->fromString('25000'),
                    'hand'          => $service['tileSequence']     ->fromTenhou('123456789m1234z'),
                    'draw'          => $service['tile']             ->fromTenhou('5z'),
                    'is_answered'   => $service['boolean']          ->fromBoolean(false),
                    'answer'        => $service['answerCollection'] ->fromNew(),
                    'best_answer'   => $service['abc']              ->fromDefault('a'),
                ]);
            });
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
                'is_hidden'     => 'integer',
                'content'       => 'json',
            ]));
            $type->view('form', $type->associative([
                'exercise_id'   => 'string',
                'title'         => 'string',
                'is_hidden'     => 'integer',
                'content'       => ['*', 'form'],
            ]));
            $type->import('new', function ($presentation) use ($service) {
                return new ProductInternal([
                    'exercise_id'   => $service['integer']          ->fromString    ($presentation),
                    'title'         => $service['string']           ->fromString    (''),
                    'is_hidden'     => $service['boolean']          ->fromBoolean   (true),
                    'content'       => $service['exerciseContent']  ->fromNew       (),
                ]);
            });
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
