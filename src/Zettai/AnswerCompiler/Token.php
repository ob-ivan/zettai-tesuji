<?php
namespace Zettai\AnswerCompiler;

class Token
{
    // const //
    
    const T_ASTERISK                = __LINE__;
    const T_NON_SPECIAL_CHARACTER   = __LINE__;
    const T_PARENTHESIS_CLOSE       = __LINE__;
    const T_PARENTHESIS_OPEN        = __LINE__;
    
    // var //
    
    private $type;
    private $value;
    private $position;
    private $length;
    
    // public //
    
    public function __construct($type, $value, $position, $length)
    {
        $this->type     = $type;
        $this->value    = $value;
        $this->position = $position;
        $this->length   = $length;
    }
    
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Unknown field "' . $name . '"', Exception::TOKEN_GET_NAME_UNKNOWN);
    }
}
