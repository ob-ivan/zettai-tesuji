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
    
    public function equals(self $operand)
    {
        return $this->type === $operand->type && $this->primitive === $operand->primitive;
    }
}
