<?php
namespace Zettai;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

class Tile implements IteratorAggregate, JsonSerializable
{
    // const //

    public static $TILES = [
        '1m', '2m', '3m', '4m', '0m', '5m', '6m', '7m', '8m', '9m',
        '1p', '2p', '3p', '4p', '0p', '5p', '6p', '7p', '8p', '9p',
        '1s', '2s', '3s', '4s', '0s', '5s', '6s', '7s', '8s', '9s',
        '1z', '2z', '3z', '4z', '5z', '6z', '7z',
    ];

    // var //

    private $tiles;

    // public //

    public function __construct ($display)
    {
        throw new Exception('Class "' . __CLASS__ . '" is deprecated', Exception::TILE_DEPRECATED);

        // Распознать масти и собрать в массив.
        $this->tiles = [];
        while (strlen($display) > 0) {
            $display = trim ($display);
            if (! preg_match ('/^(\d+)([mpsz])/', $display, $matches)) {
                break;
            }
            $display = substr ($display, strlen ($matches[0]));
            for ($i = 0, $c = strlen ($matches[1]); $i < $c; ++$i) {
                $this->tiles[] = [
                    'number' => $matches[1][$i],
                    'color'  => $matches[2],
                ];
            }
        }
    }

    public function __toString()
    {
        return $this->stringify();
    }

    // IteratorAggregate //

    public function getIterator()
    {
        return new ArrayIterator($this->tiles);
    }

    // JsonSerializable //

    public function jsonSerialize()
    {
        return $this->stringify();
    }

    // private //

    public function stringify()
    {
        $colors = [
            'm' => [],
            'p' => [],
            's' => [],
            'z' => [],
        ];
        foreach ($this->tiles as $tile) {
            $colors[$tile['color']][] = $tile['number'];
        }
        $string = '';
        foreach ($colors as $color => $numbers)  {
            if (! empty($numbers)) {
                usort($numbers, function ($a, $b) {
                    if (! $a) $a = 5;
                    if (! $b) $b = 5;
                    if ($a < $b) return -1;
                    if ($a > $b) return  1;
                    return 0;
                });
                $string .= implode('', $numbers) . $color;
            }
        }
        return $string;
    }
}
