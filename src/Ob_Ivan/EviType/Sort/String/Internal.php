<?php
/**
 * Носитель внутреннего представления для строкового типа.
 * Просто сама строка.
**/
namespace Ob_Ivan\EviType\Sort\String;

use Ob_Ivan\EviType\InternalInterface;

class Internal implements InternalInterface
{
    private $primitive;

    public function __construct($primitive)
    {
        $this->primitive = strval($primitive);
    }

    public function getPrimitive()
    {
        return $this->primitive;
    }
}
