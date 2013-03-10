<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Zettai\Type\Service;

class TypeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['types'] = new Service(['Tile', 'English', 'Eng', 'Russian', 'Rus']);

    }
    
    public function boot(Application $app)
    {
        $service = $app['types'];
        
        $service->register('roundWind', function ($service) {
            return $service->viewable([
                ['1z', 'east',  'e', 'восток', 'в'],
                ['2z', 'south', 's', 'юг',     'ю'],
            ]);
        });
        $service->register('squareWind', function ($service) {
            return $service->viewable([
                ['3z', 'west',  'w', 'запад', 'з'],
                ['4z', 'north', 'n', 'север', 'с'],
            ]);
        });
        $service->register('wind', function ($service) {
            return $service->union($service['roundWind'], $service['squareWind']);
        });
        $service->register('kyoku', function ($service) {
            return $service->product($service['roundWind'], '-', range(1, 4));
        });
        $service->register('suit', function ($service) {
            return $service->viewable([
                ['m', 'man', 'm', 'ман', 'м'],
                ['p', 'pin', 'p', 'пин', 'п'],
                ['s', 'sou', 's', 'со',  'с'],
            ]);
        });
        $service->register('dragon', function ($service) {
            return $service->viewable([
                ['5z', 'White', 'W', 'Белый',   'Б'],
                ['6z', 'Green', 'G', 'Зелёный', 'З'],
                ['7z', 'Red',   'R', 'Красный', 'К'],
            ]);
        });
        $service->register('tile', function ($service) {
            return $service->union(
                $service->product(
                    [1, 2, 3, 4, 0, 5, 6, 7, 8, 9],
                    $service['suit']
                ),
                $service['wind'],
                $service['dragon']
            );
        });
        $service->register('tileSequence', function ($service) {
            return $service->sequence($service['tile'])
            ->setHook('fromView', function ($view, $presentation) {
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
            })
            ->setHook('toView', function ($view, $internal) {
            
                $isLong = in_array($view, ['English', 'Russian']);
            
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
            });
        });
        $service->register('exerciseContents', function ($service) {
            return $service->record([
                'kyoku'     => $service['kyoku'],
                'position'  => $service['wind'],
                'turn'      => range(1, 18),
                'dora'      => $service['tile'],
                'score'     => $service->text(),
                'hand'      => $service['tileSequence'],
                'draw'      => $service['tile'],
                'discard_a' => $service['tile'],
                'answer_a'  => $service->text(),
                'discard_b' => $service['tile'],
                'answer_b'  => $service->text(),
                'discard_c' => $service['tile'],
                'answer_c'  => $service->text(),
            ]);
        });
    }
}
