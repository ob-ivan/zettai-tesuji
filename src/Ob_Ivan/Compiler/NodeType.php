<?php
namespace Ob_Ivan\Compiler;

class NodeType
{
    // conct //
    
    const FRAGMENT = __LINE__;
    
    // var //
    
    private static $registry = [];
    
    // public //
    
    public static function fragment()
    {
        return $this->get(self::FRAGMENT);
    }
    
    // private //
    
    private function get($value)
    {
        if (! isset($this->registry[$value])) {
            $this->registry[$value] = new self($value);
        }
        return $this->registry[$value];
    }
    
    private function __construct($value)
    {
        $this->value = $value;
    }
}
