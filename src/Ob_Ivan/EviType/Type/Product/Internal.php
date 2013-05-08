<?php
/**
 * Носитель внутреннего представления для значений типов,
 * являющихся перемножением других типов.
 *
 * Массив, отображающий имя компоненты в значение-объект.
 *
 *  [
 *      <index componentName> => <Value value>,
 *      ...
 *  ]
**/
namespace Ob_Ivan\EviType\Type\Product;

use ArrayAccess,
    ArrayIterator,
    IteratorAggregate;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\Value;

class Internal implements ArrayAccess, InternalInterface, IteratorAggregate
{
    // var //

    private $map;
    private $primitive = null;

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
        if (is_null($this->primitive)) {
            $primitives = [];
            foreach ($this->map as $componentName => $value) {
                $primitives[$componentName] = $value->getPrimitive();
            }
            $this->primitive = json_encode($primitives);
        }
        return $this->primitive;
    }

    // public : IteratorAggregate //

    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    // public : Internal //

    public function __construct(array $componentNameToValueMap)
    {
        foreach ($componentNameToValueMap as $componentName => $value) {
            if (! $value instanceof Value) {
                throw new Exception(
                    'Map value for key "' . $componentName . '" must be instance of Value',
                    Exception::INTERNAL_CONSTRUCT_VALUE_WRONG_TYPE
                );
            }
        }
        $this->map = $componentNameToValueMap;
    }

    public function __get($name)
    {
        return $this[$name];
    }
}
