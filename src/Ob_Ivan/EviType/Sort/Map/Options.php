<?php
/**
 * Опции отображения из типа в тип -- пара этих типов плюс свойство "полноты".
 *
 *  boolean         isTotal
 *  TypeInterface   domain
 *  TypeInterface   range
**/
namespace Ob_Ivan\EviType\Sort\Map;

use Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\IterableInterface;
use Ob_Ivan\EviType\TypeInterface;

class Options implements OptionsInterface
{
    private $isTotal;
    private $domain;
    private $range;

    // public : Options //

    public function __construct($isTotal, TypeInterface $domain, TypeInterface $range)
    {
        // Check whether domain accepts total maps.
        if ($isTotal) {
            if (! $domain instanceof IterableInterface) {
                throw new Exception('Total map cannot be defined with non-iterable domain');
            }
        }

        $this->isTotal = !! $isTotal;
        $this->domain  = $domain;
        $this->range   = $range;
    }

    public function isTotal()
    {
        return $this->isTotal;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getRange()
    {
        return $this->range;
    }
}
