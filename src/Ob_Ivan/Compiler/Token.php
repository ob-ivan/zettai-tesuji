<?php
namespace Ob_Ivan\Compiler;

class Token
{
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
