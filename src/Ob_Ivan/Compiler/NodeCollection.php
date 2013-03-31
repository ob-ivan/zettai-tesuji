<?php
namespace Ob_Ivan\Compiler;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class NodeCollection implements ArrayAccess, IteratorAggregate
{
    // var //
    
    private $isFrozen = false;
    private $nodes = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->nodes[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->nodes[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if ($this->isFrozen) {
            throw new Exception('Cannot add values to a frozen collection', Exception::NODE_COLLECTION_IS_FROZEN);
        }
        if (! $value instanceof Node) {
            throw new Exception(
                'Value must implement Node',
                Exception::NODE_COLLECTION_OFFSET_SET_VALUE_TYPE_WRONG
            );
        }
        
        // TODO: Если value -- узел-пустышка, вобрать его потомков.
        $this->nodes[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        if ($this->isFrozen) {
            throw new Exception('Cannot unset in a frozen collection', Exception::NODE_COLLECTION_IS_FROZEN);
        }
        unset($this->nodes[$offset]);
    }
    
    // public : IteratorAggregate //
    
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
    
    // public : NodeCollection //
    
    public function append(Node $node)
    {
        if ($this->isFrozen) {
            throw new Exception('Cannot append to a frozen collection', Exception::NODE_COLLECTION_IS_FROZEN);
        }
        // TODO: Если node -- узел-пустышка, вобрать его потомков.
        $this->nodes[] = $node;
    }
    
    public function freeze()
    {
        $this->isFrozen = true;
    }
}
