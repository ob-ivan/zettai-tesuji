<?php
/**
 * Контейнер и породитель перечислимых типов.
**/
namespace Zettai\Type;

use ArrayAccess;

class Service implements ArrayAccess
{
    // cosnt //
    
    const ENG       = __LINE__;
    const ENGLISH   = __LINE__;
    const RUS       = __LINE__;
    const RUSSIAN   = __LINE__;
    
    // var //
    
    /**
     * @var [<name> => <Type>]
    **/
    private $types = [];
    
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
        if (! $value instanceof Type) {
            throw new Exception('Value must be of type Type for offset "' . $offset . '"', Exception::SERVICE_SET_VALUE_WRONG_TYPE);
        }
        return $this->types[$offset];
    }
    
    public function offsetUnset($offset)
    {
        throw new Exception('Unsetting types is unsupported', Exception::SERVICE_UNSET_UNSUPPORTED);
    }
    
    // public : Service //
    
    /**
     * Создаёт новый перечислимый тип.
     *
     *  @param  [<viewIndex> => <viewValue>]                        $views
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
     *  @return Enum
    **/
    public function enum($views, $values)
    {
        return new Enum($views, $values);
    }
    
    /**
     * Создаёт новый тип, применяя звёздочку Клини к заданному типу.
     *
     *  @param  Type    $type
     *  @return Iteration
    **/
    public function iteration($type)
    {
        return new Iteration($type);
    }
    
    /**
     * Создаёт новый тип декартова произведения.
     *
     *  @param  Type    Использует переданный тип как координату.
     *  @param  array   Использует перечисленные значения (во всех видах) как координату.
     *  @param  string  Вставляет разделитель в представления (кроме примитивного).
     *  @return Product
    **/
    public function product()
    {
        return new Product(func_get_args());
    }
    
    /**
     * Создаёт новый тип объединения.
     *
     *  @param  Type    Вариант значений.
     *  @return Union
    **/
    public function union()
    {
        return new Union(func_get_args());
    }
}
