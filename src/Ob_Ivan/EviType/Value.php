<?php
namespace Ob_Ivan\EviType;

use Ob_Ivan\EviType\Type\StringifierInterface;

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

    public function __call($name, $arguments)
    {
        return $this->type->callValueMethod($this->internal, $name, $arguments);
    }

    public function __toString()
    {
        if ($this->type instanceof StringifierInterface) {
            return $this->type->stringify($this->internal);
        }
        return '[ERROR: method __toString is not supported for this type]';
    }

    public function belongsTo(TypeInterface $type)
    {
        return $this->type === $type;
    }

    public function getPrimitive()
    {
        return $this->internal->getPrimitive();
    }

    public function to($exportName)
    {
        return $this->type->to($exportName, $this->internal);
    }
}
