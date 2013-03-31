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
        throw new Exception(
            'Setting explicit offsets is not allowed; use append() instead',
            Exception::NODE_COLLECTION_OFFSET_SET_PROHIBITED
        );
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting offsets is not allowed',
            Exception::NODE_COLLECTION_OFFSET_UNSET_PROHIBITED
        );
    }
    
    // public : IteratorAggregate //
    
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
    
    // public : NodeCollection //
    
    public static function fromArray(array $array = null)
    {
        $collection = new self;
        if ($array) {
            foreach ($array as $node) {
                $collection->append($node);
            }
        }
        $collection->freeze();
        return $collection;
    }
    
    public function append(Node $node)
    {
        if ($this->isFrozen) {
            throw new Exception('Cannot append to a frozen collection', Exception::NODE_COLLECTION_IS_FROZEN);
        }
        if ($node instanceof Node\Fragment) {
            foreach ($node->children as $child) {
                $this->append($child);
            }
        } else {
            $this->nodes[] = $node;
        }
    }
    
    public function freeze()
    {
        $this->isFrozen = true;
    }
}
