<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

class TypeContainer implements ArrayAccess
{
    /**
     * @var [<string name> => <Type type>]
    **/
    private $registry = [];
    
    /**
     * @var [<string name> => <Type(args...) producer>]
    **/
    private $types    = [];
    
    public function offsetExists($offset) {}
    public function offsetGet($offset) {}
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    
    public function __get($name) {}
    public function __isset($name) {}
    
    public function register($name, callable $producer) {}
}
