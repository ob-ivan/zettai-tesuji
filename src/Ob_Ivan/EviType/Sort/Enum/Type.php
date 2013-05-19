<?php
namespace Ob_Ivan\EviType\Sort\Enum;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\IterableInterface,
    Ob_Ivan\EviType\Sort\StringifierInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface, StringifierInterface
{
    // public : IterableInterface //

    public function each()
    {
        $values = [];
        foreach ($this->getOptions() as $primitive => $name) {
            $values[] = $this->produceValue(new Internal($primitive));
        }
        return $values;
    }

    // public : StringifierInterface //

    public function stringify(InternalInterface $internal)
    {
        return $this->getOptions()[$internal->getPrimitive()];
    }

    // public : ParentType //

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

    // public : Type : view factory //

    public function dictionary($map)
    {
        return new View\Dictionary($map);
    }
}
