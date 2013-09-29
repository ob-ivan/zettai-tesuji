<?php
/**
 * Опции типа, составляющего конечные последовательности
 * из другого типа, это просто сам вложенный тип.
**/
namespace Ob_Ivan\EviType\Sort\Sequence;

use Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\TypeInterface;

class Options implements OptionsInterface
{
    // var //

    private $type;

    // public : Options //

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
}
