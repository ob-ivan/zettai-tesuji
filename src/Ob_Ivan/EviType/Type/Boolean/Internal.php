<?php
/**
 * Носитель внутреннего представления для булевого типа.
 * Просто само истинностное значение.
**/
namespace Ob_Ivan\EviType\Type\Boolean;

use Ob_Ivan\EviType\InternalInterface;

class Internal implements InternalInterface
{
    private $primitive;

    public function __construct($primitive)
    {
        $this->primitive = !! $primitive;
    }

    public function getPrimitive()
    {
        return $this->primitive;
    }
}
