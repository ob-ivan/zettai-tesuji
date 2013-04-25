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

    public function __construct (TypeInterface $type, InternalInterface $internal)
    {
        $this->type     = $type;
        $this->internal = $internal;
    }

    public function getPrimitive()
    {
        return $this->internal->getPrimitive();
    }
}
