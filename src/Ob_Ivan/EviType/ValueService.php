<?php
namespace Ob_Ivan\EviType;

class ValueService
{
    /**
     *  @var [<string primitive> => <Value value>]
    **/
    private $registry = [];

    /**
     * @var TypeInterface
    **/
    private $type;

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }

    public function produce(InternalInterface $internal)
    {
        $primitive = $internal->getPrimitive();
        if (! isset($this->registry[$primitive])) {
            $this->registry[$primitive] = new Value($this->type, $internal);
        }
        return $this->registry[$primitive];
    }
}
