<?php
namespace Ob_Ivan\Compiler;

abstract class Node
{
    // var //
    
    private $token;
    
    /**
     * @var NodeCollection
    **/
    private $children;
    
    /**
     * Позиция, измеренная в токенах.
    **/
    private $position;
    
    /**
     * Длина в токенах.
    **/
    private $length;
    
    // public //
    
    public function __construct(Token $token = null, NodeCollection $children = null, $position, $length)
    {
        $this->token    = $token;
        $this->children = $children;
        $this->position = $position;
        $this->length   = $length;
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
