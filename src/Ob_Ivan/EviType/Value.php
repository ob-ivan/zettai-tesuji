<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;
use Ob_Ivan\EviType\Type\DereferencerInterface,
    Ob_Ivan\EviType\Type\StringifierInterface;

class Value implements ArrayAccess
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
        if (! $this->type instanceof DereferencerInterface) {
            throw new Exception(
                'Dereferencing is not supported for this type',
                Exception::VALUE_OFFSET_EXISTS_NOT_SUPPORTED
            );
        }
        return $this->type->dereferenceExists($this->internal, $offset);
    }

    public function offsetGet($offset)
    {
        if (! $this->type instanceof DereferencerInterface) {
            throw new Exception(
                'Dereferencing is not supported for this type',
                Exception::VALUE_OFFSET_GET_NOT_SUPPORTED
            );
        }
        return $this->type->dereferenceGet($this->internal, $offset);
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
