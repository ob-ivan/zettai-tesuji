<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Zettai\Type\Service;

class TypeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['types'] = new Service([
            'Tile', 'English', 'Eng', 'Russian', 'Rus'
        ]);

    }
    
    public function boot(Application $app)
    {
        $service = $app['types'];
        
        $roundWind = $service->viewable([
            'Tile', 'English', 'Eng', 'Russian', 'Rus'
        ], [
            ['1z', 'east',  'e', 'восток', 'в'],
            ['2z', 'south', 's', 'юг',     'ю'],
        ]);
        $squareWind = $service->viewable([
            'Tile', 'English', 'Eng', 'Russian', 'Rus'
        ], [
            ['3z', 'west',  'w', 'запад', 'з'],
            ['4z', 'north', 'n', 'север', 'с'],
        ]);
        $service['wind'] = $service->union($roundWind, $squareWind);
        $service['kyoku'] = $service->product($roundWind, '-', range(1, 4));
        $suit = $service->viewable([
            'Tile', 'English', 'Eng', 'Russian', 'Rus',
        ], [
            ['m', 'man', 'm', 'ман', 'м'],
            ['p', 'pin', 'p', 'пин', 'п'],
            ['s', 'sou', 's', 'со',  'с'],
        ]);
        $dragon = $service->viewable([
            'Tile', 'English', 'Eng', 'Russian', 'Rus',
        ], [
            ['5z', 'White', 'W', 'Белый',   'Б'],
            ['6z', 'Green', 'G', 'Зелёный', 'З'],
            ['7z', 'Red',   'R', 'Красный', 'К'],
        ]);
        $service['tile'] = $service->union(
            $service->product(
                [1, 2, 3, 4, 0, 5, 6, 7, 8, 9],
                $suit
            ),
            $service['wind'],
            $dragon
        );
        $service['tileSequence'] = $service->sequence($service['tile'])
        ->addEvent('beforeFromView')
        ->beforeFromView(function ($view, $presentation) {
            $prepared = [];
            while (! empty($presentation)) {
                $oldLength = strlen($presentation);
                $presentation = trim($presentation);
                if (preg_match('/^(\d+)(\D*)/', $presentation, $matches)) {
                    for ($i = 0, $count = strlen($matches[1]); $i < $count; ++$i) {
                        $prepared[] = $matches[1][$i] . $matches[2];
                    }
                    $presentation = substr($presentation, strlen($matches[0]));
                } elseif (preg_match('/\D+/', $presentation, $matches)) {
                    $prepared[] = $matches[0];
                    $presentation = substr($presentation, strlen($matches[0]));
                }
                if ($oldLength <= strlen($presentation)) {
                    break;
                }
            }
            return preg_replace('/\s+/', '', implode('', $prepared));
        })
        ->addEvent('afterToView')
        ->afterToView(function ($view, $presentation) {
            $prepared = [];
            while (! empty($presentation)) {
                $oldLength = strlen($presentation);
                $presentation = trim($presentation);
                if (preg_match_all('/^\d(\D+)/', $presentation, $matches)) {
                    print '<pre>' . __FILE__ . ':' . __LINE__ . ': matches = ' . print_r($matches, true) . '</pre>'; die; // debug
                    $presentation = substr($presentation, strlen($matches[0]));
                }
                if ($oldLength <= strlen($presentation)) {
                    break;
                }
            }
            return implode('', $prepared);
        });
    }
}
