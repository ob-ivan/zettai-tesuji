<?php
/**
 * Контейнер и породитель перечислимых типов.
**/
namespace Zettai\Type;

use ArrayAccess;

class Service implements ArrayAccess, ServiceInterface
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
        return isset($this->types[$offset]);
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
        if (isset($this->types[$offset])) {
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
    
    public function __construct(array $views)
    {
        $this['view'] = $this->type($views);
    }
    
    public function getViewByName($name)
    {
        return $this->views->from($name);
    }
    
    // public : Service utilities //
    
    public function __get($name)
    {
        return $this[$name];
    }
    
    /**
     * Регистрирует тип для ленивой инициализации.
     *
     *  @param  string                      $name       Имя, под которым тип будет известен.
     *  @param  Service -> TypeInterface    $factory    Функция, которая породит тип по запросу.
    **/
    public function register($name, $factory)
    {
        if (isset($this->registry[$name])) {
            throw new Exception('Type "' . $name . '" is already registered', Exception::SERVICE_REGISTER_NAME_ALREADY_EXISTS);
        }
        $this->registry[$name] = $factory;
    }
    
    // public : type factories //
    
    public function enum(array $values)
    {
        return new Enum($this, $values);
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
    
    public function singleton($value)
    {
        return new Singleton($this, $value);
    }
    
    public function text()
    {
        return new Text($this);
    }
    
    public function type($candidate)
    {
        if ($candidate instanceof TypeInterface) {
            return $candidate;
        }
        if (is_array($candidate)) {
            return $this->enum($candidate);
        }
        return $this->singleton($candidate);
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
    
    /**
     * Создаёт новый перечислимый тип с настраиваемым представлением.
     *
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
     *
     *  @return Viewable
    **/
    public function viewable(array $values)
    {
        return new Viewable($this, $this['view']->each(), $values);
    }
}
