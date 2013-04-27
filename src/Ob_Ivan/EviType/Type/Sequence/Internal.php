<?php
/**
 * Носитель внутреннего представления для значений типов,
 * являющихся конечными последовательностями из другого типа.
 *
 * Массив, отображающий номер позиции (отсёт от нуля) в значение-объект.
 *
 *  [
 *      <index index> => <Value value>,
 *      ...
 *  ]
**/
namespace Ob_Ivan\EviType\Type\Sequence;

use ArrayAccess,
    ArrayIterator,
    IteratorAggregate;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\Value;

class Internal implements ArrayAccess, InternalInterface, IteratorAggregate
{
    // var //

    private $map;

    // public : ArrayAccess //

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
        throw new Exception('Modifying components is not allowed', Exception::INTERNAL_OFFSET_SET_PROHIBITED);
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Modifying components is not allowed', Exception::INTERNAL_OFFSET_UNSET_PROHIBITED);
    }

    // public : InternalInterface //

    public function getPrimitive()
    {
        $primitives = [];
        foreach ($this->map as $index => $value) {
            $primitives[$index] = $value->getPrimitive();
        }
        return json_encode($primitives);
    }

    // public : IteratorAggregate //

    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    // public : Internal //

    public function __construct(array $indexToValueMap)
    {
        foreach ($indexToValueMap as $index => $value) {
            if (! $value instanceof Value) {
                throw new Exception(
                    'Map value for index "' . $index . '" must be instance of Value',
                    Exception::INTERNAL_CONSTRUCT_VALUE_WRONG_TYPE
                );
            }
        }
        $this->map = $indexToValueMap;
    }
}
