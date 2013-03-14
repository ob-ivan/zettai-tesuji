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
            ->register('index',   'dictionary', array_keys($values))
            ->register('default', 'dictionary', $values)
        ;
    }
    
    public function each()
    {
        $return = [];
        foreach ($this->values as $index => $views) {
            $return[$index] = $this->value($index);
        }
        return $return;
    }
    
    public function equals($internalA, $internalB)
    {
        return $internalA === $internalB;
    }
}
