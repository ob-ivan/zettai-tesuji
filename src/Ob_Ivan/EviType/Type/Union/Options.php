<?php
/**
 * Опции объединения типов -- массив,
 * отправляющий имя варианта в его тип.
 *
 *  [
 *      <string variantName> => <TypeInterface type>,
 *      ...
 *  ]
**/
namespace Ob_Ivan\EviType\Type\Union;

use ArrayAccess,
    ArrayIterator,
    IteratorAggregate;
use Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\TypeInterface;

class Options implements ArrayAccess, IteratorAggregate, OptionsInterface
{
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

    public function __construct(array $variantNameToTypeMap)
    {
        foreach ($variantNameToTypeMap as $variantName => $type) {
            if (! $type instanceof TypeInterface) {
                throw new Exception(
                    'Map value for key "' . $variantName . '" must implement TypeInterface',
                    Exception::OPTIONS_CONSTRUCT_TYPE_WRONG_TYPE
                );
            }
        }
        $this->map = $variantNameToTypeMap;
    }
}
