<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

class ViewFactory implements ArrayAccess
{
    /**
     * @var [<string name> => <View(args...) producer>]
    **/
    private $producers = [];
    
    public function offsetExists($offset) {}
    public function offsetGet($offset) {}
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    
    public function __call($name, $args) {}
    public function __invoke($name, $args) {}
}
