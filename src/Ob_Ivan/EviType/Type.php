<?php
namespace Ob_Ivan\EviType;

class Type implements TypeInterface
{
    /**
     * @var TypeSort
    **/
    private $sort;
    
    /**
     * @var ViewService
    **/
    private $view;
    
    public function __construct(TypeSortInterface $sort)
    {
        $this->sort = $sort;
    }
}
