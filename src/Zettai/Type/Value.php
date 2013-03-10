<?php
namespace Zettai\Type;

class Value implements ValueInterface
{
    // var //
    
    private $type;
    private $internal;
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        if (! $this->type instanceof DereferenceableInterface) {
            return false;
        }
        return $this->type->dereferenceExists($this->internal, $offset);
    }
    
    public function offsetGet($offset)
    {
        if (! $this->type instanceof DereferenceableInterface) {
            return null;
        }
        return $this->type->dereference($this->internal, $offset);
    }
    
    public function offsetSet($offset, $value)
    {
        throw new Exception('Setting offsets is not supported for value type', Exception::VALUE_SET_UNSUPPORTED);
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting offsets is not supported for value type', Exception::VALUE_UNSET_UNSUPPORTED);
    }
    
    // public : ValueInterface //
    
    public function __construct(TypeInterface $type, $internal)
    {
        $this->type     = $type;
        $this->internal = $internal;
    }
    
    /**
     * Реализует волшебные методы:
     *  - to<ViewName>()
    **/
    public function __call($name, $args)
    {
        if (preg_match('/^to(\w+)$/i', $name, $matches)) {
            return $this->type->toViewByName($matches[1], $this->internal);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::VALUE_CALL_METHOD_UNKNOWN);
    }
    
    public function __get($name)
    {
        return $this[$name];
    }
    
    public function __toString()
    {
        return $this->type->toString($this->internal);
    }
    
    public function equals($operand)
    {
        if (! $operand instanceof self) {
            $operand = $this->type->from($operand);
        }
        if ($this->type !== $operand->type) {
            return false;
        }
        return $this->type->equals($this->internal, $operand->internal);
    }
    
    public function is(TypeInterface $type)
    {
        return $this->type === $type;
    }
    
    public function toPrimitive()
    {
        return $this->type->toPrimitive($this->internal);
    }
    
    public function toString()
    {
        return $this->__toString();
    }
    
    public function toView($view)
    {
        return $this->type->toView($view, $this->internal);
    }
}
