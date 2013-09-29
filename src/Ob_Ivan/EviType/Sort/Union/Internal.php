<?php
namespace Ob_Ivan\EviType\Sort\Union;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\Value;

class Internal implements InternalInterface
{
    private $primitive = null;
    private $variantName;
    private $value;

    // public : InternalInterface //

    public function getPrimitive()
    {
        if (is_null($this->primitive)) {
            $this->primitive = json_encode([
                $this->variantName,
                $this->value->getPrimitive(),
            ]);
        }
        return $this->primitive;
    }

    // public : Internal //

    public function __construct($variantName, Value $value)
    {
        $this->variantName = $variantName;
        $this->value = $value;
    }

    public function getName()
    {
        return $this->variantName;
    }

    public function getValue()
    {
        return $this->value;
    }
}
