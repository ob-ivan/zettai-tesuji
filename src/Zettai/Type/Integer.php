<?php
namespace Zettai\Type;

class Integer extends Type
{
    public function fromPrimitive($presentation)
    {
        return $this->value(intval($presentation));
    }
    
    public function fromView($view, $presentation)
    {
        return $this->fromPrimitive($presentation);
    }
    
    public function toView($view, $internal)
    {
        return $internal;
    }
}
