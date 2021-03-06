<?php
namespace Ob_Ivan\EviType\Sort\Enum;

use ArrayIterator;
use IteratorAggregate;
use Ob_Ivan\EviType\InternalInterface;
use Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\IterableInterface;
use Ob_Ivan\EviType\Sort\StringifierInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements
    IterableInterface,
    IteratorAggregate,
    StringifierInterface
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

    // public : IteratorAggregate //

    public function getIterator()
    {
        return new ArrayIterator($this->each());
    }

    // public : StringifierInterface //

    public function stringify(InternalInterface $internal)
    {
        return strval($this->getOptions()[$internal->getPrimitive()]);
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

    public function integer()
    {
        return new View\Integer();
    }
}
