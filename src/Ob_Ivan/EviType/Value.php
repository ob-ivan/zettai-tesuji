<?php
namespace Ob_Ivan\EviType;

class Value
{
    /**
     * @var Type
    **/
    private $type;
    
    /**
     * @var mixed
    **/
    private $internal;
    
    public function __construct (TypeInterface $type, $internal)
    {
        $this->type     = $type;
        $this->internal = $internal;
    }
}
