<?php
/**
 * Контейнер и породитель перечислимых типов.
**/
namespace Zettai\Type;

use ArrayAccess;

class Service implements ArrayAccess
{
    // var //
    
    /**
     * @var [<name> => <Type>]
    **/
    private $types = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->types[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->types[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if (isset($this->types[$offset])) {
            throw new Exception('Type "' . $offset . '" already exists', Exception::SERVICE_SET_OFFSET_ALREADY_EXISTS);
        }
        if (! $value instanceof Type) {
            throw new Exception('Value must be of type Type for offset "' . $offset . '"', Exception::SERVICE_SET_VALUE_WRONG_TYPE);
        }
        return $this->types[$offset];
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting types is unsupported', Exception::SERVICE_UNSET_UNSUPPORTED);
    }
}
