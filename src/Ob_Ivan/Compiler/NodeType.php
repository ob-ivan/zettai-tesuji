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
        return self::get(self::FRAGMENT);
    }
    
    // private //
    
    private static function get($value)
    {
        if (! isset(self::$registry[$value])) {
            self::$registry[$value] = new self($value);
        }
        return self::$registry[$value];
    }
    
    private function __construct($value)
    {
        $this->value = $value;
    }
}
