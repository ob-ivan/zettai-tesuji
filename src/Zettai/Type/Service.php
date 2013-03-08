<?php
/**
 * Контейнер и породитель перечислимых типов.
**/
namespace Zettai\Type;

use ArrayAccess;

class Service implements ArrayAccess, ServiceInterface
{
    // cosnt //
    
    // TODO: Заменить на перечисление, передаваемое в конструктор.
    const ENG       = __LINE__;
    const ENGLISH   = __LINE__;
    const RUS       = __LINE__;
    const RUSSIAN   = __LINE__;
    
    private static $VIEWS = [
        self::ENG,
        self::ENGLISH,
        self::RUS,
        self::RUSSIAN,
    ];
    
    // var //
    
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
    
    public function getViewByName($viewName)
    {
        $constant = __CLASS__ . '::' . strtoupper($viewName);
        if (! defined($constant)) {
            return null;
        }
        return constant($constant);
    }
    
    public function getViews()
    {
        return self::$VIEWS;
    }
    
    // public : Service //
    
    public function __get($name)
    {
        return $this[$name];
    }
    
    /**
     * Создаёт новый перечислимый тип.
     *
     *  @param  [<viewIndex> => <viewValue>]                        $views
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
     *
     *  @return Enum
    **/
    public function enum(array $views, array $values)
    {
        return new Enum($this, $views, $values);
    }
    
    /**
     * Создаёт новый тип, применяя звёздочку Клини к заданному типу.
     *
     *  @param  TypeInterface   $type
     *  @return Iteration
    **/
    public function iteration(TypeInterface $type)
    {
        return new Iteration($this, $type);
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
