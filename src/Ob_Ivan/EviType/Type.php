<?php
namespace Ob_Ivan\EviType;

class Type implements TypeInterface
{
    private $arguments;
    
    /**
     * @var TypeSort
    **/
    private $sort;
    
    /**
     * @var ViewService
    **/
    private $view;
    
    public function __construct(TypeSortInterface $sort, array $arguments = null)
    {
        $this->sort         = $sort;
        $this->arguments    = $arguments;
        
        $this->view         = $sort->view($this);
    }
    
    public function __get($name) {
        if ($name === 'view') {
            return $this->$name;
        }
        throw new Exception(
            'Unknown field "' . $name . '"',
            Exception::TYPE_GET_NAME_UNKNOWN
        );
    }
}
