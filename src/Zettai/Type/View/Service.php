<?php
namespace Zettai\Type\View;

use Zettai\Type\Type\TypeInterface;

class Service implements ServiceInterface
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
            throw new Exception('View "' . $offset . '" already exists', Exception::SERVICE_SET_OFFSET_ALREADY_EXISTS);
        }
        if (! $value instanceof ViewInterface) {
            throw new Exception('Value must implement ViewInterface for offset "' . $offset . '"', Exception::SERVICE_SET_VALUE_WRONG_TYPE);
        }
        $this->views[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting offsets is not supported', Exception::SERVICE_UNSET_NOT_SUPPORTED);
    }
    
    // public : ServiceInterface //
    
    public function each()
    {
        // Дозагрузить все представления во views.
        foreach ($this->registry as $name => $provider) {
            $this[$name];
        }
        return $this->views;
    }
    
    /**
     * Запоминает свойства представления для отложенной инстанциации.
     *
     *  @param  string                          $name
     *
     *  @param  ViewInterface(ViewService)    $provider
     * или:
     *  @param  string  $viewClass
     *  @params mixed   аргументы
    **/
    public function register($name, $provider)
    {
        if (isset($this->registry[$name])) {
            throw new Exception('View "' . $name . '" is already registered', Exception::SERVICE_REGISTER_NAME_ALREADY_EXISTS);
        }
        if (isset($this->views[$name])) {
            throw new Exception('View "' . $name . '" already exists', Exception::SERVICE_REGISTER_NAME_ALREADY_EXISTS);
        }
        if (! is_callable($provider)) {
            $args = array_slice(func_get_args(), 2);
            $provider = return function ($service) use ($provider, $args) {
                return $this->providers[$provider]($service, $args)
            }
        }
        $this->registry[$name] = $provider;
        return $this;
    }
    
    // public : Service //
    
    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }
    
    public function __get($name)
    {
        if ($name === 'type') {
            return $this->type;
        }
        return $this[$name];
    }
}
