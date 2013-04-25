<?php
namespace Ob_Ivan\EviType\Type\Enum;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\StringifierInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements StringifierInterface
{
    // public : StringifierInterface //

    public function stringify(InternalInterface $internal)
    {
        return $this->getOptions()[$internal->getPrimitive()];
    }

    // public : Type //

    public function __construct(OptionsInterface $options = null)
    {
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be instance of Options',
                Exception::TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE
            );
        }
        parent::__construct($options);
    }

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

    // public : view factory //

    public function dictionary($map)
    {
        return new View\Dictionary($map);
    }

    // public : type specific //

    public function each()
    {
        $values = [];
        foreach ($this->getOptions() as $name) {
            $values[] = $this->from('default', $name);
        }
        return $values;
    }
}
