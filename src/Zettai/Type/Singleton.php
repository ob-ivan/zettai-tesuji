<?php
namespace Zettai\Type;

class Singleton extends Type
{
    const PRIMITIVE = 0;
    
    private $value;
    
    public function __construct (ServiceInterface $service, $value)
    {
        parent::__construct($service);
        
        $this->value = $value;
    }
    
    public function each()
    {
        return [self::PRIMITIVE => $this->fromPrimitive(self::PRIMITIVE)];
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
        return $this->value(self::PRIMITIVE);
    }
    
    public function toPrimitive($internal)
    {
        return self::PRIMITIVE;
    }
    
    public function toView($view, $internal)
    {
        return $this->value;
    }
}