<?php
namespace Ob_Ivan\EviType;

class ViewFactory
{
    /**
     * @var [<string name> => <TypeSortInterface() producer>]
    **/
    private $registry = [];

    /**
     * @var [<string name> => <TypeSortInterface sort>]
    **/
    private $sorts = [];

    public function has($name)
    {
        return isset($this->sorts[$name]) || isset($this->registry[$name]);
    }

    public function produce($name, $arguments)
    {
        if (! isset($this->sorts[$name])) {
            if (! isset($this->registry[$name])) {
                throw new Exception(
                    'Unknown view sort "' . $name . '"',
                    Exception::VIEW_FACTORY_PRODUCE_NAME_UNKNOWN
                );
            }
            $sort = $this->registry[$name]();
            if (! $sort instanceof ViewSortInterface) {
                throw new Exception(
                    'Produced view sort "' . $name . '" must implement ViewSortInterface',
                    Exception::VIEW_FACTORY_PRODUCE_SORT_WRONG_TYPE
                );
            }
            $this->sorts[$name] = $sort;
        }
        return $this->sorts[$name]->produce($arguments);
    }

    /**
     * Регистрирует один или несколько сортов представлений.
     *
     *  @param  string              $name
     *  @param  ViewSortInterface() $producer
     * OR
     *  @param  [string => ViewSortInterface()] $producerMap
    **/
    public function register($name, callable $producer = null)
    {
        if (is_array($name)) {
            foreach ($name as $sortName => $producer) {
                $this->register($sortName, $producer);
            }
        } else {
            if ($this->has($name)) {
                throw new Exception(
                    'View sort "' . $name . '" already exists',
                    Exception::VIEW_FACTORY_REGISTRY_NAME_ALREADY_EXISTS
                );
            }
            $this->registry[$name] = $producer;
        }
        return $this;
    }
}
