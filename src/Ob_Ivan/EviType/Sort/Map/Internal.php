<?php
/**
 * Носитель внутреннего представления для значений типов,
 * являющихся отображениями из одного типа в другой.
**/
namespace Ob_Ivan\EviType\Sort\Map;

use ArrayAccess;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\Value;

class Internal implements ArrayAccess, InternalInterface
{
    // var //

    /**
     *  @var [<index domainValue->getPrimitive()> => <Value domainValue>]
    **/
    private $domainMap;

    /**
     *  @var [<index domainValue->getPrimitive()> => <Value rangeValue>]
    **/
    private $rangeMap;

    /**
     * Кэш для примитивного значения.
     *
     *  @var string json([<domainValue->getPrimitive()> => <rangeValue->getPrimitive()>])
    **/
    private $primitive = null;

    // public : ArrayAccess //

    public function offsetExists($offset)
    {
        if (! $offset instanceof Value) {
            throw new Exception('Offset must be instance of Value', Exception::INTERNAL_OFFSET_WRONG_TYPE);
        }
        $primitive = $offset->getPrimitive();
        if (! (isset($this->domainMap[$primitive]) && $this->domainMap[$primitive] === $offset)) {
            return false;
        }
        return isset($this->rangeMap[$primitive]);
    }

    public function offsetGet($offset)
    {
        if (! $offset instanceof Value) {
            throw new Exception('Offset must be instance of Value', Exception::INTERNAL_OFFSET_WRONG_TYPE);
        }
        $primitive = $offset->getPrimitive();
        if (! (isset($this->domainMap[$primitive]) && $this->domainMap[$primitive] === $offset)) {
            return null;
        }
        return $this->rangeMap[$primitive];
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
            foreach ($this->rangeMap as $domainPrimitive => $rangeValue) {
                $primitives[$domainPrimitive] = $rangeValue->getPrimitive();
            }
            $this->primitive = json_encode($primitives);
        }
        return $this->primitive;
    }

    // public : Internal //

    /**
     *  @param  [[<Value domainValue>, <Value rangeValue>], ...]    $pairs
    **/
    public function __construct(array $pairs)
    {
        $this->domainMap = [];
        $this->rangeMap  = [];
        foreach ($pairs as $position => $pair) {
            list($domainValue, $rangeValue) = $pair;
            if (! $domainValue instanceof Value) {
                throw new Exception(
                    'Domain value at position ' . $position . ' must be instance of Value',
                    Exception::INTERNAL_CONSTRUCT_DOMAIN_VALUE_WRONG_TYPE
                );
            }
            if (! $rangeValue instanceof Value) {
                throw new Exception(
                    'Range value at position ' . $position . ' must be instance of Value',
                    Exception::INTERNAL_CONSTRUCT_RANGE_VALUE_WRONG_TYPE
                );
            }
            $primitive = $domainValue->getPrimitive();
            $this->domainMap[$primitive] = $domainValue;
            $this->rangeMap [$primitive] = $rangeValue;
        }
    }

    public function keys()
    {
        return new ArrayIterator($this->domainMap);
    }
}
