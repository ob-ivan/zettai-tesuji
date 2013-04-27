<?php
/**
 * Опции произведения типов -- это массив,
 * отправляющий имя компоненты в её тип.
 *
 *  [
 *      <index componentName> => <TypeInterface type>,
 *      ...
 *  ]
**/
namespace Ob_Ivan\EviType\Type\Product;

use ArrayAccess,
    ArrayIterator,
    IteratorAggregate;
use Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\TypeInterface;

class Options implements ArrayAccess, IteratorAggregate, OptionsInterface
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
        throw new Exception('Modifying options is not allowed', Exception::OPTIONS_OFFSET_SET_PROHIBITED);
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Modifying options is not allowed', Exception::OPTIONS_OFFSET_UNSET_PROHIBITED);
    }

    // public : IteratorAggregate //

    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    // public : Options //

    public function __construct(array $componentNameToTypeMap)
    {
        foreach ($componentNameToTypeMap as $componentName => $type) {
            if (! $type instanceof TypeInterface) {
                throw new Exception(
                    'Map value for key "' . $componentName . '" must implement TypeInterface',
                    Exception::OPTIONS_CONSTRUCT_TYPE_WRONG_TYPE
                );
            }
        }
        $this->map = $componentNameToTypeMap;
    }
}
