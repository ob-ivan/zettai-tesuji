<?php
namespace Zettai;

use ArrayObject as BaseArrayObject;

/**
 * Расширяет поведение обычного ArrayObject, рекурсивно применяя
 * то же преобразование ко всем потомкам.
**/
class ArrayObject extends BaseArrayObject
{
    const FLAG_READ_ONLY = 0x80;
    
    public function __construct ($input, $flags = 0, $iterator_class = 'ArrayIterator')
    {
        $flags |= BaseArrayObject::ARRAY_AS_PROPS;
        parent::__construct (
            self::objectifyChildren (
                $input,
                $flags,
                $iterator_class
            ),
            $flags,
            $iterator_class
        );
    }
    
    private static function objectifyChildren ($input, $flags, $iterator_class)
    {
        foreach ($input as $prop => $value) {
            if (is_array($value) || is_object($value)) {
                $input[$prop] = new self($value, $flags, $iterator_class);
            }
        }
        return $input;
    }
}
