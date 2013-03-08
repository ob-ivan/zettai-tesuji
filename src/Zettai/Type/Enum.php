<?php
namespace Zettai\Type;

class Enum extends Type
{
    /**
     *  @var [<index> => <value>]
    **/
    private $values;
    
    public function __construct (Service $service, array $values)
    {
        parent::__construct($service);
        
        $this->values = $values;
    }
    
    public function each()
    {
        $return = [];
        foreach ($this->values as $primitive => $views) {
            $return[$primitive] = $this->fromPrimitive($primitive);
        }
        return $return;
    }
    
    public function fromView($view, $presentation)
    {
        $index = array_search($presentation, $this->values);
        if (false === $index) {
            return null;
        }
        return $this->fromPrimitive($index);
    }
    
    public function fromPrimitive($primitive)
    {
        if (! isset($this->values[$primitive])) {
            return null;
        }
        return new Value($this, $primitive);
    }
    
    public function toView($view, $primitive)
    {
        if (! isset($this->values[$primitive])) {
            throw new Exception(
                'Unknown value "' . $primitive . '"',
                Exception::ENUM_TO_VIEW_UNSUPPORTED_PRIMITIVE
            );
        }
        return $this->values[$primitive];
    }
}
