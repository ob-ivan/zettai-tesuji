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
        if ($this->getViewName($offset)) {
            return true;
        }
        if (! $this->type instanceof DereferenceableInterface) {
            return false;
        }
        return $this->type->dereferenceExists($this->internal, $offset);
    }
    
    public function offsetGet($offset)
    {
        $viewName = $this->getViewName($offset);
        if ($viewName) {
            return $this->toViewByName($viewName);
        }
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
        $viewName = $this->getViewName($name);
        if ($viewName) {
            return $this->toViewByName($viewName);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::VALUE_CALL_METHOD_UNKNOWN);
    }
    
    /**
     * Реализует обращение к волшебному методу to<ViewName> без скобочек.
    **/
    public function __get($name)
    {
        return $this[$name];
    }
    
    public function __isset($name)
    {
        return isset($this[$name]);
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
    
    public function toViewByName($viewName)
    {
        return $this->type->toViewByName($viewName, $this->internal);
    }
    
    // private //
    
    // Извлекает название представления из функции преобразования в представление.
    private function getViewName($name)
    {
        if (preg_match('/^to(\w+)$/i', $name, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
