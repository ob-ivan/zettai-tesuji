<?php
namespace Ob_Ivan\EviType\Sort\Integer;

use Ob_Ivan\EviType\InternalInterface;
use Ob_Ivan\EviType\Sort\StringifierInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements StringifierInterface
{
    // public : StringifierInterface //

    public function stringify(InternalInterface $internal)
    {
        return strval($internal->getPrimitive());
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
}
