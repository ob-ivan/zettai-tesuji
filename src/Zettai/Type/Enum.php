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
        foreach ($this->values as $index => $value) {
            if (0 === strpos($presentation, strval($value))) {
                return $this->fromPrimitive($index);
            }
        }
        return null;
    }
    
    public function fromPrimitive($primitive)
    {
        if (! isset($this->values[$primitive])) {
            return null;
        }
        return $this->value($primitive);
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
