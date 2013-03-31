<?php
namespace Ob_Ivan\Compiler;

abstract class Node
{
    // var //
    
    /**
     * Позиция, измеренная в токенах.
    **/
    private $position;
    
    /**
     * Длина в токенах.
    **/
    private $length;
    
    /**
     * @var NodeCollection
    **/
    private $children;
    
    private $value;
    
    // public //
    
    public function __construct($position, $length, NodeCollection $children = null, $value = null)
    {
        $this->position = $position;
        $this->length   = $length;
        $this->children = $children;
        $this->value    = $value;
    }
    
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Unknown field "' . $name . '"', Exception::NODE_GET_NAME_UNKNOWN);
    }
    
    abstract public function build();
}
