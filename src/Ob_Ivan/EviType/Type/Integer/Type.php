<?php
namespace Ob_Ivan\EviType\Type\Integer;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Type\StringifierInterface;
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
