<?php
namespace Zettai\AnswerCompiler;

abstract class Token
{
    // var //
    
    private $value;
    
    // public //
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Unknown field "' . $name . '"', Exception::TOKEN_GET_NAME_UNKNOWN);
    }
}
