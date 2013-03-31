<?php
namespace Ob_Ivan\Compiler;

use ArrayAccess;

class ParsingRuleSet implements ArrayAccess
{
    // var //
    
    private $rules = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->rules[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->rules[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if (! $value instanceof ParsingRuleInterface) {
            throw new Exception(
                'Value must implement ParsingRuleInterface',
                Exception::PARSING_RULE_SET_OFFSET_SET_VALUE_TYPE_WRONG
            );
        }
        $this->rules[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->rules[$offset]);
    }
    
    // public : ParsingRuleSet //
    
    public function addRules(array $rules)
    {
        foreach ($rules as $name => $rule) {
            $this->rules[$name] = $rule;
        }
    }
}
