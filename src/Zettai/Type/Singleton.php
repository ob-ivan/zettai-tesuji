<?php
namespace Zettai\Type;

class Singleton extends Type
{
    const PRIMITIVE = 0;
    
    private $value;
    
    public function __construct (Service $service, $value)
    {
        parent::__construct($service);
        
        $this->value = $value;
    }
    
    public function each()
    {
        $return = [];
        $return[self::PRIMITIVE] = $this->fromPrimitive(self::PRIMITIVE);
        return $return;
    }
    
    public function fromView($view, $presentation)
    {
        if (0 === strpos($presentation, $this->value)) {
            return $this->fromPrimitive(self::PRIMITIVE);
        }
        return null;
    }
    
    public function fromPrimitive($primitive)
    {
        return new Value($this, $primitive);
    }
    
    public function toView($view, $primitive)
    {
        return $this->value;
    }
}
