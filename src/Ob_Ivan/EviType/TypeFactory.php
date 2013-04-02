<?php
namespace Ob_Ivan\EviType;

class TypeFactory
{
    /**
     * @var [<string name> => <TypeSortInterface producer()>]
    **/
    private $registry = [];
    
    /**
     * @var [<string name> => <TypeSortInterface typeSort>]
    **/
    private $sorts = [];
    
    public function produce($name, $arguments)
    {
        if (! isset($this->sorts[$name])) {
            if (! isset($this->registry[$name])) {
                throw new Exception(
                    'Unknown type sort "' . $name . '"',
                    Exception::TYPE_FACTORY_PRODUCE_NAME_UNKNOWN
                );
            }
            $sort = $this->registry[$name]();
            if (! $sort instanceof TypeSortInterface) {
                throw new Exception(
                    'Value for "' . $name . '" must implement TypeSortInterface',
                    Exception::TYPE_FACTORY_PRODUCE_SORT_WRONG_TYPE
                );
            }
            $this->sorts[$name] = $sort;
        }
        return $this->sorts[$name]->produce($arguments);
    }
    
    /**
     * Регистрирует один или несколько сортов типа.
     *
     *  @param  string              $name
     *  @param  TypeSortInterface() $producer
     * OR
     *  @param  [string => TypeSortInterface()] $producerMap
    **/
    public function register($name, callable $producer = null)
    {
        if (is_array($name)) {
            foreach ($name as $sortName => $producer) {
                $this->register($sortName, $producer);
            }
        } else {
            if (isset($this->sorts[$name]) || isset($this->registry[$name])) {
                throw new Exception(
                    'Type sort "' . $name . '" already exists',
                    Exception::TYPE_FACTORY_REGISTRY_NAME_ALREADY_EXISTS
                );
            }
            $this->registry[$name] = $producer;
        }
        return $this;
    }
}
