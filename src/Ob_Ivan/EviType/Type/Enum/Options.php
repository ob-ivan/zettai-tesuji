<?php
/**
 * Опции перечислимого типа -- это просто массив,
 * отправляющий примитивное значени в имя.
**/
namespace Ob_Ivan\EviType\Type\Enum;

use ArrayAccess,
    ArrayIterator,
    IteratorAggregate;
use Ob_Ivan\EviType\OptionsInterface;

class Options implements ArrayAccess, IteratorAggregate, OptionsInterface
{
    private $map;

    public function __construct(array $primitiveToNameMap)
    {
        $this->map = $primitiveToNameMap;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    public function offsetExists($offset)
    {
        return isset($this->map[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->map[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('Modifying options is not allowed', Exception::OPTIONS_OFFSET_SET_PROHIBITED);
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Modifying options is not allowed', Exception::OPTIONS_OFFSET_UNSET_PROHIBITED);
    }
}
