<?php
namespace Zettai\AnswerCompiler;

class Token
{
    // const //
    
    const TYPE_TEXT       = __LINE__;
    const TYPE_ANNOTATION = __LINE__;
    
    private static $TYPES = [
        self::TYPE_TEXT       => 1,
        self::TYPE_ANNOTATION => 1,
    ];
    
    // var //
    
    private $type;
    private $content;
    
    // public //
    
    public function __construct($type, $content)
    {
        if (! isset(self::$TYPES[$type])) {
            throw new Exception('Unknown token type "' . $type . '"', Exception::TYPE_UNKNOWN);
        }
        $this->type = $type;
        $this->content = $content;
    }
    
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Unknown field "' . $name . '"', Exception::GET_UNKNOWN_FIELD);
    }
}
