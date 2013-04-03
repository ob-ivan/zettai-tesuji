<?php
namespace Ob_Ivan\EviType;

class ViewService implements ViewServiceInterface
{
    // var //

    /**
     * @var ViewFactory
    **/
    private $factory;

    /**
     * @var ViewFactory
    **/
    private $fallback;

    /**
     * @var [<string name> => <ViewInterface(self) producer>]
    **/
    private $registry = [];

    /**
     * @var [<string name> => <ViewInterface view>]
    **/
    private $views = [];

    // public //

    public function offsetExists($offset)
    {
        return isset($this->views[$offset]) ||
            isset($this->registry[$offset]) ||
            isset($this->fallback[$offset]);
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
            $this[$offset] = $this->registry[$offset]();
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
        if (! $value instanceof ViewInterface) {
            throw new Exception(
                'Value for offset "' . $offset . '" must implement ViewInterface',
                Exception::VIEWSERVICE_OFFSET_SET_VALUE_WRONG_TYPE
            );
        }
        $this->types[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting views is not supported',
            Exception::VIEW_SERVICE_OFFSET_UNSET_UNSUPPORTED
        );
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

    public function __construct(ViewFactory $fallback = null)
    {
        $this->factory  = new ViewFactory();
        $this->fallback = $fallback;
    }

    public function __call($name, $args)
    {
        if ($this->factory->has($name)) {
            return $this->factory->produce($name, $args);
        }
        if ($this->fallback->has($name)) {
            return $this->fallback->produce($name, $args);
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
}
