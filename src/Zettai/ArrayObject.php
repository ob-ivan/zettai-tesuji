<?php
namespace Zettai;

use ArrayObject as BaseArrayObject;

/**
 * Расширяет поведение обычного ArrayObject, рекурсивно применяя
 * то же преобразование ко всем потомкам.
**/
class ArrayObject extends BaseArrayObject
{
    const READ_ONLY = 0x80;
    
    private $readOnly = false;
    
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
        if ($flags & self::READ_ONLY) {
            $this->readOnly = true;
        }
    }
    
    public function __set ($name, $value)
    {
        if ($this->readOnly) {
            throw new Exception ('Array object is read only', Exception::ARRAY_OBJECT_READ_ONLY);
        }
        parent::__set($name, $value);
    }
    
    public function offsetSet($offset, $value)
    {
        if ($this->readOnly) {
            throw new Exception ('Array object is read only', Exception::ARRAY_OBJECT_READ_ONLY);
        }
        parent::offsetSet($offset, $value);
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
