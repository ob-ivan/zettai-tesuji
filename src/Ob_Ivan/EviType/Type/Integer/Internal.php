<?php
/**
 * Носитель внутреннего представления для целочисленного типа.
 * Просто само это число.
**/
namespace Ob_Ivan\EviType\Type\Integer;

use Ob_Ivan\EviType\InternalInterface;

class Internal implements InternalInterface
{
    private $primitive;

    public function __construct($primitive)
    {
        $this->primitive = intval($primitive);
    }

    public function getPrimitive()
    {
        return $this->primitive;
    }
}
