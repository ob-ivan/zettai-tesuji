<?php
namespace Ob_Ivan\EviType;

use ArrayAccess,
    IteratorAggregate;
use Ob_Ivan\EviType\Sort\StringifierInterface,
    Ob_Ivan\EviType\Sort\ValueIteratorInterface;

class Value implements ArrayAccess, IteratorAggregate
{
    // var //

    /**
     * @var Type
    **/
    private $type;

    /**
     * @var mixed
    **/
    private $internal;

    // public : ArrayAccess //

    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception(
            'Setting offsets is not allowed',
            Exception::VALUE_OFFSET_SET_NOT_ALLOWED
        );
    }

    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting offsets is not allowed',
            Exception::VALUE_OFFSET_UNSET_NOT_ALLOWED
        );
    }

    // public : IteratorAggregate //

    public function getIterator()
    {
        if ($this->type instanceof ValueIteratorInterface) {
            return $this->type->getValueIterator($this->internal);
        }
        throw new Exception(
            'Value of this type cannot be iterated',
            Exception::VALUE_GET_ITERATOR_NOT_SUPPORTED
        );
    }

    // public : Value //

    public function __construct (TypeInterface $type, InternalInterface $internal)
    {
        $this->type     = $type;
        $this->internal = $internal;
    }

    public function __call($name, $arguments)
    {
        return $this->type->callValueMethod($this->internal, $name, $arguments);
    }

    public function __get($name)
    {
        return $this->type->get($name, $this->internal);
    }

    public function __isset($name)
    {
        return $this->type->exists($name);
    }

    public function __toString()
    {
        if ($this->type instanceof StringifierInterface) {
            return $this->type->stringify($this->internal);
        }
        return '[ERROR: method __toString is not supported for this type]';
    }

    public function belongsTo(TypeInterface $type)
    {
        return $this->type === $type;
    }

    public function getPrimitive()
    {
        return $this->internal->getPrimitive();
    }

    public function to($exportName)
    {
        return $this->type->to($exportName, $this->internal);
    }
}
