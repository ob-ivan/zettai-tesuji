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
     * @var [<name> => <TypeInterface>]
    **/
    private $types = [];
    
    private $views;
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->types[$offset]);
    }
    
    public function offsetGet($offset)
    {
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
        $this->views = $this->type($views);
    }
    
    public function getViews()
    {
        return $this->views->each();
    }
    
    public function getViewByName($name)
    {
        return $this->views->from($name);
    }
    
    // public : Service //
    
    public function __get($name)
    {
        return $this[$name];
    }
    
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
     *  @param  [<viewIndex> => <viewValue>]                        $views
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
     *
     *  @return Viewable
    **/
    public function viewable(array $views, array $values)
    {
        return new Viewable($this, $views, $values);
    }
}
