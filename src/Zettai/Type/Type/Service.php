<?php
/**
 * Контейнер и породитель перечислимых типов.
**/
namespace Zettai\Type\Type;

class Service implements ServiceInterface
{
    // var //
    
    /**
     * @var [<name> => <factory>]
    **/
    private $registry = [];
    
    /**
     * @var [<name> => <TypeInterface>]
    **/
    private $types = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->types[$offset]) || isset($this->registry[$offset]);
    }
    
    public function offsetGet($offset)
    {
        if (! isset($this->types[$offset])) {
            if (! isset($this->registry[$offset])) {
                return null;
            }
            $this->types[$offset] = $this->registry[$offset]($this);
        }
        return $this->types[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        if (isset($this[$offset])) {
            throw new Exception('Type "' . $offset . '" already exists', Exception::SERVICE_SET_OFFSET_ALREADY_EXISTS);
        }
        if (! $value instanceof TypeInterface) {
            throw new Exception('Value must implement TypeInterface for offset "' . $offset . '"', Exception::SERVICE_SET_VALUE_WRONG_TYPE);
        }
        $this->types[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting types is unsupported', Exception::SERVICE_UNSET_UNSUPPORTED);
    }
    
    // public : ServiceInterface //
    
    public function from($candidate)
    {
        if ($candidate instanceof TypeInterface) {
            return $candidate;
        }
        if (is_array($candidate)) {
            return $this->enum($candidate);
        }
        return null;
    }
    
    /**
     * Регистрирует тип для ленивой инициализации.
     *
     *  @param  string                      $name       Имя, под которым тип будет известен.
     *  @param  Service -> TypeInterface    $factory    Функция, которая породит тип по запросу.
    **/
    public function register($name, $factory)
    {
        if (isset($this[$name])) {
            throw new Exception('Type "' . $name . '" already exists', Exception::SERVICE_REGISTER_NAME_ALREADY_EXISTS);
        }
        $this->registry[$name] = $factory;
        return $this;
    }
    
    // public : Service //
    
    public function __get($name)
    {
        return $this[$name]; 
    }
    
    // public : predefined types //
    
    public function boolean()
    {
        return $this['boolean'];
    }
    
    public function integer()
    {
        return $this['integer'];
    }
    
    public function text()
    {
        return $this['text'];
    }
    
    // public : complex type factories //
    
    public function enum(array $values)
    {
        return new Enum($this, $values);
    }
    
    /**
     * Создаёт тип тотального отображения из области определения (domain)
     * в область значений (range).
    **/
    public function map($domain, $range)
    {
        return new Map($this, $domain, $range);
    }
    
    /**
     * Создаёт новый тип декартова произведения.
     *
     * Может быть сколько угодно параметров:
     *  @param  TypeInterface   Использует переданный тип как координату.
     *  @param  array           Использует перечисленные значения (во всех видах) как координату.
     *  @param  string          Вставляет разделитель в представления (кроме примитивного).
     *
     *  @return Product
    **/
    public function product()
    {
        return new Product($this, func_get_args());
    }
    
    public function record($fields)
    {
        return new Product($this, $fields);
    }
    
    /**
     * Создаёт новый тип конечных последовательностей из элементов указанного типа.
     *
     *  @param  TypeInterface   $type
     *  @return Sequence
    **/
    public function sequence(TypeInterface $type)
    {
        return new Sequence($this, $type);
    }
    
    /**
     * Создаёт новый тип объединения.
     *
     * Может быть сколько угодно параметров:
     *  @param  TypeInterface   Вариант значений.
     *
     *  @return Union
    **/
    public function union()
    {
        return new Union($this, func_get_args());
    }
}