<?php
namespace Ob_Ivan\EviType;

class TypeFactory
{
    /**
     * @var [<string name> => <TypeBuilderInterface producer()>]
    **/
    private $registry = [];

    /**
     * @var [<string name> => <TypeBuilderInterface builder>]
    **/
    private $builders = [];

    public function produce($name, array $arguments)
    {
        if (! isset($this->builders[$name])) {
            if (! isset($this->registry[$name])) {
                throw new Exception(
                    'Unknown type sort "' . $name . '"',
                    Exception::TYPE_FACTORY_PRODUCE_NAME_UNKNOWN
                );
            }
            $builder = $this->registry[$name]();
            if (! $builder instanceof TypeSortInterface) {
                throw new Exception(
                    'Value for "' . $name . '" must implement TypeSortInterface',
                    Exception::TYPE_FACTORY_PRODUCE_SORT_WRONG_TYPE
                );
            }
            $this->builders[$name] = $builder;
        }
        return $this->builders[$name]->produce($arguments);
    }

    /**
     * Регистрирует один или несколько сортов типа.
     *
     *  @param  string                  $name
     *  @param  TypeBuilderInterface()  $producer
     * OR
     *  @param  [string => TypeBuilderInterface()] $producerMap
    **/
    public function register($name, callable $producer = null)
    {
        if (is_array($name)) {
            foreach ($name as $sortName => $producer) {
                $this->register($sortName, $producer);
            }
        } else {
            if (isset($this->builders[$name]) || isset($this->registry[$name])) {
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
