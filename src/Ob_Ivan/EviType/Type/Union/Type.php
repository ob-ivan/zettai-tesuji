<?php
namespace Ob_Ivan\EviType\Type\Union;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Type\IterableInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface
{
    // public : IterableInterface //

    public function each()
    {
        $values = [];
        foreach ($this->getOptions() as $variantName => $type) {
            if (! $type instanceof IterableInterface) {
                throw new Exception(
                    'Variant "' . $variantName . '" cannot be iterated',
                    Exception::TYPE_EACH_VARIANT_NOT_ITERABLE
                );
            }
            foreach ($type->each() as $value) {
                $values[] = $value;
            }
        }
        return $values;
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

    public function select(array $map)
    {
        return new View\Select($map);
    }
}
