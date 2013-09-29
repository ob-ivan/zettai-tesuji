<?php
/**
 * Носитель внутреннего представления для значений перечислимых типов.
 * Просто значение ключа в соответствующем Options.
**/
namespace Ob_Ivan\EviType\Sort\Enum;

use Ob_Ivan\EviType\InternalInterface;

class Internal implements InternalInterface
{
    private $primitive;

    public function __construct($primitive)
    {
        $this->primitive = $primitive;
    }

    public function getPrimitive()
    {
        return $this->primitive;
    }
}
