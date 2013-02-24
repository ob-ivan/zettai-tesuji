<?php
namespace Zettai;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

class Pai implements IteratorAggregate, JsonSerializable
{
    private $pais;
    
    public function __construct ($display)
    {
        // Распознать масти и собрать в массив.
        $this->pais = [];
        while (strlen($display) > 0) {
            $display = trim ($display);
            if (! preg_match ('/^(\d+)([mpsz])/', $display, $matches)) {
                break;
            }
            $display = substr ($display, strlen ($matches[0]));
            for ($i = 0, $c = strlen ($matches[1]); $i < $c; ++$i) {
                $this->pais[] = [
                    'number' => $matches[1][$i],
                    'color'  => $matches[2],
                ];
            }
        }
    }
    
    public function getIterator()
    {
        return new ArrayIterator($this->pais);
    }
    
    public function jsonSerialize()
    {
        $colors = [
            'm' => [],
            'p' => [],
            's' => [],
            'z' => [],
        ];
        foreach ($this->pais as $pai) {
            $colors[$pai['color']][] = $pai['number'];
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
