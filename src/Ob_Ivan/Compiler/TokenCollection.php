<?php
namespace Ob_Ivan\Compiler;

use ArrayAccess;

class TokenCollection implements ArrayAccess
{
    // var //
    
    private $tokens = [];
    private $length = 0;
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->tokens[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        throw new Exception(
            'Setting tokens at offsets is not allowed, use append() instead', 
            Exception::TOKEN_COLLECTION_OFFSET_SET_PROHIBITED
        );
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting tokens at offsets is not allowed', 
            Exception::TOKEN_COLLECTION_OFFSET_UNSET_PROHIBITED
        );
    }
    
    // public : TokenCollection //
    
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        throw new Exception('Unknown field "' . $name . '"', Exception::TOKEN_COLLECTION_GET_NAME_UNKNOWN);
    }
    
    public function append(Token $token)
    {
        $this->tokens[] = $token;
        $this->length++;
    }
}
