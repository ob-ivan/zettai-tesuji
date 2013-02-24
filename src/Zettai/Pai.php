<?php
namespace Zettai;

use ArrayIterator;
use IteratorAggregate;

class Pai implements IteratorAggregate
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
}
