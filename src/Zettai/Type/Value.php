<?php
namespace Zettai\Type;

class Value implements ValueInterface
{
    private $type;
    private $primitive;
    
    public function __construct(TypeInterface $type, $primitive)
    {
        $this->type      = $type;
        $this->primitive = $primitive;
    }
    
    /**
     * Реализует волшебные методы:
     *  - to<ViewName>()
    **/
    public function __call($name, $args)
    {
        if (preg_match('/^to(\w+)$/i', $name, $matches)) {
            return $this->type->toViewByName($matches[1], $this->primitive);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::VALUE_CALL_METHOD_UNKNOWN);
    }
    
    public function equals($operand)
    {
        if (! $operand instanceof self) {
            $operand = $this->type->from($operand);
        }
        if ($this->type !== $operand->type) {
            return false;
        }
        return $this->type->equals($this->primitive, $operand->primitive);
    }
    
    public function is(TypeInterface $type)
    {
        return $this->type === $type;
    }
    
    public function toView($view)
    {
        return $this->type->toView($view, $this->primitive);
    }
}
