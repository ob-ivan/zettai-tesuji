<?php
namespace Ob_Ivan\EviType;

class ViewContainer implements ViewContainerInterface
{
    // var //
    
    /**
     * @var ViewContainer
    **/
    private $parent;
    
    /**
     * @var [<string name> => <View(args...) producer>]
    **/
    private $registry = [];
    
    /**
     * @var [<string name> => <View view>]
    **/
    private $views = [];
    
    // public //
    
    public function offsetExists($offset) {}
    public function offsetGet($offset) {}
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    
    public function __construct(ViewContainerInterface $parent = null)
    {
        $this->parent = $parent;
    }
    
    public function __get($name) {}
    public function __isset($name) {}
    public function register($name, callable $producer) {}
}
