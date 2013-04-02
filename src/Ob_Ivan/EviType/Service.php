<?php
namespace Ob_Ivan\EviType;

class Service implements ServiceInterface
{
    /**
     * @var TypeFactory
    **/
    private $factory;
    
    /**
     * @var [<string name> => <Type type>]
    **/
    private $registry = [];
    
    /**
     * @var [<string name> => <Type(args...) producer>]
    **/
    private $types    = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->types[$offset]) || isset($this->registry[$offset]);
    }
    
    public function offsetGet($offset)
    {
        if (! isset($this->types[$offset])) {
            if (! isset($this->registry[$offset])) {
                throw new Exception(
                    'Unknown type "' . $offset . '"',
                    Exception::SERVICE_OFFSET_GET_OFFSET_UNKNOWN
                );
            }
            $this[$offset] = $this->registry[$offset]($this);
        }
        return $this->types[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if (isset($this[$name])) {
            throw new Exception(
                'Type "' . $name . '" already exists',
                Exception::SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS
            );
        }
        if (! $value instanceof TypeInterface) {
            throw new Exception(
                'Value for offset "' . $offset . '" must implement TypeInterface',
                Exception::SERVICE_OFFSET_SET_VALUE_WRONG_TYPE
            );
        }
        $this->types[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting types is not supported',
            Exception::SERVICE_OFFSET_UNSET_UNSUPPORTED
        );
    }
    
    // public : ServiceInterface //
    
    public function register($name, callable $producer)
    {
        if (isset($this[$name])) {
            throw new Exception(
                'Type "' . $name . '" already exists',
                Exception::SERVICE_REGISTER_NAME_ALREADY_EXISTS
            );
        }
        $this->registry[$name] = $producer;
    }
    
    // public : Service //
    
    public function __construct()
    {
        $this->factory = new TypeFactory();
    }
    
    public function __call($name, $args)
    {
        return $this->factory->produce($name, $args);
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
