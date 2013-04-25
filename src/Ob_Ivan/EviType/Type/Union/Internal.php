<?php
namespace Ob_Ivan\EviType\Type\Union;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\Value;

class Internal implements InternalInterface
{
    private $variantName;
    private $value;

    // public : InternalInterface //

    public function getPrimitive()
    {
        return json_encode([
            $this->variantName,
            $this->value->getPrimitive(),
        ]);
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
