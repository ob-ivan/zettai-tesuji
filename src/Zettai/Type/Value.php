<?php
namespace Zettai\Type;

class Value implements ValueInterface
{
    private $type;
    private $internal;
    
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
    
    public function project($coordinate)
    {
        if ($this->type instanceof ProjectiveInterface) {
            return $this->type->project($coordinate, $this->internal);
        }
        throw new Exception('Projection for this type is not supported', Exception::VALUE_PROJECT_UNSUPPORTED);
    }
    
    public function toView($view)
    {
        return $this->type->toView($view, $this->internal);
    }
}
