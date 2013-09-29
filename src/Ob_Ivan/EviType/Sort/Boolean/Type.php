<?php
namespace Ob_Ivan\EviType\Sort\Boolean;

use Ob_Ivan\EviType\InternalInterface;
use Ob_Ivan\EviType\Sort\IterableInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface
{
    private $each;

    // public : IterableInterface //

    public function each()
    {
        if (is_null($this->each)) {
            $this->each = $this->generateEach();
        }
        return $this->each;
    }

    // public : ParentType //

    public function callValueMethod(InternalInterface $internal, $name, array $arguments)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be instance of Internal',
                Exception::TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE
            );
        }
        return parent::callValueMethod($internal, $name, $arguments);
    }

    // private //

    public function generateEach()
    {
        $each = [];
        foreach ([false, true] as $boolean) {
            $each[] = $this->produceValue(new Internal($boolean));
        }
        return $each;
    }
}
