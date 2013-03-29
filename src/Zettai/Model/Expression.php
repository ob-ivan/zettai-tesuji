<?php
namespace Zettai\Model;

class Expression
{
    // const //
    
    // simple
    const TYPE_ROOT         = __LINE__;
    const TYPE_CONST        = __LINE__; // TODO
    const TYPE_FIELD        = __LINE__;
    const TYPE_PARAMETER    = __LINE__;
    // functions
    const TYPE_COUNT        = __LINE__;
    const TYPE_MAX          = __LINE__;
    const TYPE_MIN          = __LINE__;
    // comparison
    const TYPE_EQUALS       = __LINE__;
    const TYPE_GREATER_THAN = __LINE__;
    const TYPE_LESS_THAN    = __LINE__;
    // logic
    const TYPE_AND          = __LINE__;
    
    private static $METHOD_TO_TYPE = [
        'count'         => self::TYPE_COUNT,
        'max'           => self::TYPE_MAX,
        'min'           => self::TYPE_MIN,
        'equals'        => self::TYPE_EQUALS,
        'greaterThan'   => self::TYPE_GREATER_THAN,
        'lessThan'      => self::TYPE_LESS_THAN,
        'andx'          => self::TYPE_AND,
    ];
    
    // public //
    
    public function __call($name, $arguments)
    {
        if (isset(self::$METHOD_TO_TYPE[$name])) {
            $expressions = [];
            foreach ($arguments as $argument) {
                $expressions[] = self::create($argument);
            }
            return new self(self::$METHOD_TO_TYPE[$name], $expressions);
        }
        throw new Exception('Unknown method "' . $name . '"', Exception::EXPRESSION_CALL_NAME_UNKNOWN);
    }
    
    public function __toString()
    {
        return $this->toString();
    }
    
    public static function create($expression = null)
    {
        if ($expression instanceof self) {
            return $expression;
        }
        if (is_null($expression)) {
            return new self(self::TYPE_ROOT);
        }
        if (is_callable($expression)) {
            return $expression(self::create());
        }
        if (is_numeric($expression)) {
            return new self(self::TYPE_CONST, [$expression]);
        }
        if (preg_match('/^:(\w+)$/', $expression, $matches)) {
            return new self(self::TYPE_PARAMETER, [$matches[1]]);
        }
        return new self(self::TYPE_FIELD, [$expression]);
    }
    
    public function toString()
    {
        switch ($this->type) {
        
            // simple //
            
            case self::TYPE_CONST:
                return '"' . $this->arguments[0] . '"';
                
            case self::TYPE_FIELD:
                return '`' . $this->arguments[0] . '`';
                
            case self::TYPE_PARAMETER:
                return ':' . $this->arguments[0];
                
            // functions //
            
            case self::TYPE_COUNT:
                return 'COUNT(' . $this->arguments[0] . ')';
                
            case self::TYPE_MAX:
                return 'MAX(' . $this->arguments[0] . ')';
                
            case self::TYPE_MIN:
                return 'MIN(' . $this->arguments[0] . ')';
                
            // comparison //
            
            case self::TYPE_EQUALS:
                return '(' . $this->arguments[0] . ') = (' . $this->arguments[1] . ')';
                
            case self::TYPE_GREATER_THAN:
                return '(' . $this->arguments[0] . ') > (' . $this->arguments[1] . ')';
                
            case self::TYPE_LESS_THAN:
                return '(' . $this->arguments[0] . ') < (' . $this->arguments[1] . ')';
                
            // logic //
            
            case self::TYPE_AND:
                return '(' . $this->arguments[0] . ') AND (' . $this->arguments[1] . ')';
        }
        throw new Exception('Unknown type "' . $this->type . '"', Exception::EXPRESSION_TO_STRING_TYPE_UNKNOWN);
    }
    
    // private //
    
    private function __construct($type, array $arguments = [])
    {
        $this->type      = $type;
        $this->arguments = $arguments;
    }
}
