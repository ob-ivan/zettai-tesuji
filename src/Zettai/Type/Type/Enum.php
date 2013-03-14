<?php
namespace Zettai\Type\Type;

class Enum extends Type
{
    /**
     *  @var [<index> => <value>]
    **/
    private $values;
    
    public function __construct (ServiceInterface $typeService, array $values)
    {
        parent::__construct($typeService);
        
        $this->values = $values;
        
        $this->view
            ->register('primitive', 'dictionary', array_keys($values))
            ->register('name',      'dictionary', $values)
        ;
    }
    
    public function each()
    {
        $return = [];
        foreach ($this->values as $primitive => $views) {
            $return[$primitive] = $this->value($primitive);
        }
        return $return;
    }
}
