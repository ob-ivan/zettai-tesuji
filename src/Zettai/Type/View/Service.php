<?php
namespace Zettai\Type;

class ViewContainer implements ArrayAccess
{
    /**
     * @var [<string viewName> => <callable viewProvider>]
    **/
    private $registry = [];
    
    /**
     * @var TypeInterface
    **/
    private $type;
    
    /**
     * @var [<string viewName> => <ViewInterface view>]
    **/
    private $views = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->views[$offset]) || isset($this->registry[$offset]);
    }
    
    public function offsetGet($offset)
    {
        if (! isset($this->views[$offset])) {
            if (! isset($this->registry[$offset])) {
                return null;
            }
            $this->views[$offset] = $this->registry[$offset]($this);
        }
        return $this->views[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if (isset($this[$offset])) {
            throw new Exception('View "' . $offset . '" already exists', Exception::VIEW_CONTAINER_SET_ALREADY_EXISTS);
        }
        if (! $value instanceof ViewInterface) {
            throw new Exception('Value must implement ViewInterface for offset "' . $offset . '"', Exception::VIEW_CONTAINER_SET_VALUE_WRONG_TYPE);
        }
        $this->views[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting offsets is not supported', Exception::VIEW_CONTAINER_UNSET_NOT_SUPPORTED);
    }
    
    // public : ViewContainer //
    
    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }
    
    public function __get($name)
    {
        return $this[$name];
    }
    
    /**
     * Запоминает свойства представления для отложенной инстанциации.
     *
     *  @param  string                          $name
     *
     *  @param  ViewInterface(ViewContainer)    $provider
     * или:
     *  @param  string  $viewClass
     *  @params mixed   аргументы
    **/
    public function register($name, $provider)
    {
        if (isset($this->registry[$name])) {
            throw new Exception('View "' . $name . '" is already registered', Exception::VIEW_CONTAINER_REGISTER_NAME_ALREADY_EXISTS);
        }
        if (isset($this->views[$name])) {
            throw new Exception('View "' . $name . '" already exists', Exception::VIEW_CONTAINER_REGISTER_NAME_ALREADY_EXISTS);
        }
        if (! is_callable($provider)) {
            $args = array_slice(func_get_args(), 2);
            $factory = $provider;
            $provider = function ($viewService) use ($factory, $args) {
                return call_user_func_array([$viewService, $factory], $args);
            };
        }
        $this->registry[$name] = $provider;
        return $this;
    }
}
