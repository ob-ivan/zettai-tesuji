<?php
namespace Zettai\Type;

class Text extends Type
{
    public function fromPrimitive($presentation)
    {
        return $this->value($presentation);
    }
    
    public function fromView($view, $presentation)
    {
        return $this->fromPrimitive($presentation);
    }
    
    public function toPrimitive($internal)
    {
        return $internal;
    }
    
    public function toView($view, $internal)
    {
        return $internal;
    }
}
