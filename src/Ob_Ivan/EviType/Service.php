<?php
namespace Ob_Ivan\EviType;

class Service implements ServiceInterface
{
    /**
     * @var TypeContainer
    **/
    private $container;
    
    /**
     * @var TypeFactory
    **/
    private $factory;
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return $this->container->offsetExists($offset);
    }
    
    public function offsetGet($offset)
    {
        return $this->container->offsetGet($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        return $this->container->offsetSet($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        return $this->container->offsetUnset($offset);
    }
    
    // public : ServiceInterface //
    
    public function register($name, callable $producer)
    {
        return $this->container->register($name, $producer);
    }
    
    // public : Service //
    
    public function __construct()
    {
        $this->container = new TypeContainer();
        $this->factory   = new TypeFactory();
    }
    
    public function __get($name)
    {
        return $this[$name];
    }
    
    public function __call($name, $args)
    {
        return $this->factory->produce($name, $args);
    }
}
