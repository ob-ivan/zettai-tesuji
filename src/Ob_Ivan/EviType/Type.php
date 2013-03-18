<?php
namespace Ob_Ivan\EviType;

class Type implements TypeInterface
{
    /**
     * @var TypeSort
    **/
    private $sort;
    
    /**
     * @var ViewContainer
    **/
    private $views;
    
    /**
     * @var ViewFactory
    **/
    private $viewFactory;
    
    public function __construct(TypeSortInterface $sort)
    {
        $this->sort = $sort;
    }
}
