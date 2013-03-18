<?php
namespace Ob_Ivan\EviType;

class Service
{
    /**
     * @var TypeContainer
    **/
    private $container;
    
    /**
     * @var TypeFactory
    **/
    private $factory;
    
    public function __construct()
    {
        $this->container = new TypeContainer();
        $this->factory   = new TypeFactory();
    }
}
