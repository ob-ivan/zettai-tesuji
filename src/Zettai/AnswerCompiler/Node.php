<?php
namespace Zettai\AnswerCompiler;

class Node
{
    // var //
    
    private $token;
    
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
    
    public function __construct(Token $token = null, array $children, $position, $length)
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
    
    public function build()
    {
        if ($this->token) {
            return $this->token->value;
        }
        
        $output = [];
        foreach ($this->children as $child) {
            $output[] = $child->build();
        }
        return implode('', $output);
    }
    
    public static function produce($className, Token $token = null, array $children, $position, $length)
    {
        $class = __CLASS__ . ($className ? '\\' . $className : '');
        return new $class($token, $children, $position, $length);
    }
}
