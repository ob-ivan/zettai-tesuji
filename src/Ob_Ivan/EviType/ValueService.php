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

    public function produce($internal)
    {
        $primitive = json_encode($internal);
        if (! isset($this->registry[$primitive])) {
            $this->registry[$primitive] = new Value($this->type, $internal);
        }
        return $this->registry[$primitive];
    }
}
