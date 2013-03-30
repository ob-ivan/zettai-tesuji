<?php
namespace Zettai\AnswerCompiler;

class Node
{
    // var //
    
    private $content;
    
    /**
     * Позиция, измеренная в токенах.
    **/
    private $position;
    
    /**
     * Длина в токенах.
    **/
    private $length;
    
    // public //
    
    public function __construct($content, $position, $length)
    {
        $this->content  = $content;
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
    
    public function build()
    {
        return $this->content;
    }
    
    public static function produce($className, $content, $position, $length)
    {
        $class = __CLASS__ . ($className ? '\\' . $className : '');
        return new $class($content, $position, $length);
    }
}
