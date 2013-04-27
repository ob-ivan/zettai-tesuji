<?php
namespace Ob_Ivan\EviType;

use ArrayIterator;

class ViewService implements ViewServiceInterface
{
    // var //

    /**
     * @var ViewFactory
    **/
    private $factory;

    /**
     * @var [<string name> => <ViewInterface(self) producer>]
    **/
    private $registry = [];

    /**
     * @var [<string name> => <ViewInterface view>]
    **/
    private $views = [];

    // public : ArrayAccess //

    public function offsetExists($offset)
    {
        return isset($this->views[$offset]) ||
            isset($this->registry[$offset]) ||
            $this->factory->has($offset);
    }

    public function offsetGet($offset)
    {
        if (! isset($this->views[$offset])) {
            if (! isset($this->registry[$offset])) {
                throw new Exception(
                    'Unknown view "' . $offset . '"',
                    Exception::VIEW_SERVICE_OFFSET_GET_OFFSET_UNKNOWN
                );
            }
            $this->set($offset, $this->registry[$offset]());
        }
        return $this->views[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (isset($this[$offset])) {
            throw new Exception(
                'View "' . $offset . '" already exists',
                Exception::VIEW_SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS
            );
        }
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting views is not supported',
            Exception::VIEW_SERVICE_OFFSET_UNSET_UNSUPPORTED
        );
    }

    // public : IteratorAggregate //

    public function getIterator()
    {
        foreach ($this->registry as $name => $producer) {
            if (! isset($this->views[$name])) {
                $this->set($name, $this->registry[$name]());
            }
        }
        return new ArrayIterator($this->views);
    }

    // public : ViewServiceInterface //

    /**
     * Регистрирует один или несколько производителей представлений.
     *
     *  @param  string              $name
     *  @param  ViewInterface(self) $producer
     * OR
     *  @param  [string => ViewInterface(self)] $producerMap
     *
     *  @return self
    **/
    public function register($name, callable $producer = null)
    {
        if (is_array($name)) {
            foreach ($name as $viewName => $producer) {
                $this->register($viewName, $producer);
            }
        } else {
            if (isset($this[$name])) {
                throw new Exception(
                    'View "' . $name . '" already exists',
                    Exception::VIEW_SERVICE_REGISTER_NAME_ALREADY_EXISTS
                );
            }
            $this->registry[$name] = $producer;
        }
        return $this;
    }

    // public //

    public function __construct(ViewFactory $factory = null)
    {
        $this->factory = $factory;
    }

    public function __call($name, $args)
    {
        if ($this->factory->has($name)) {
            return function () use ($name, $args) { return $this->factory->produce($name, $args); };
        }
        throw new Exception(
            'Unknown view sort "' . $name . '"',
            Exception::VIEW_SERVICE_CALL_NAME_UNKNOWN
        );
    }

    public function __get($name)
    {
        return $this[$name];
    }

    public function __isset($name)
    {
        return isset($this[$name]);
    }

    // private //

    private function set($offset, $value)
    {
        if (! $value instanceof ViewInterface) {
            die; // debug
            throw new Exception(
                'Value for offset "' . $offset . '" must implement ViewInterface',
                Exception::VIEW_SERVICE_SET_VALUE_WRONG_TYPE
            );
        }
        $this->views[$offset] = $value;
    }
}
