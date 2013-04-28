<?php
/**
 * Опции отображения из типа в тип -- пара этих типов.
 *
 *  TypeInterface domain
 *  TypeInterface range
**/
namespace Ob_Ivan\EviType\Type\Map;

use Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\TypeInterface;

class Options implements OptionsInterface
{
    private $domain;
    private $range;

    // public : Options //

    public function __construct(TypeInterface $domain, TypeInterface $range)
    {
        $this->domain = $domain;
        $this->range  = $range;
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
