<?php
namespace Zettai\Type;

class Value
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
            return $this->type->toViewByName($name, $this->primitive);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::VALUE_CALL_METHOD_UNKNOWN);
    }
    
    public function equals(self $operand)
    {
        return $this->type === $operand->type && $this->primitive === $operand->primitive;
    }
}
