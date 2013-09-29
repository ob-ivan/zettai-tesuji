<?php
namespace Ob_Ivan\EviType\Sort\Map;

use Iterator as IteratorInterface;

class Iterator implements IteratorInterface
{
    /**
     *  @var [<index domainValue->getPrimitive()> => <Value domainValue>]
    **/
    private $domainMap;

    /**
     *  @var [<index domainValue->getPrimitive()> => <Value rangeValue>]
    **/
    private $rangeMap;

    /**
     *  @var [<integer index> => <index domainValue->getPrimitive()>]
    **/
    private $keys;

    /**
     *  @var integer
    **/
    private $current;

    public function __construct(array $domainMap, array $rangeMap)
    {
        $keys = array_keys($domainMap);
        if ($keys != array_keys($rangeMap)) {
            throw new Exception(
                'Domain map and range map mu have identical keys',
                Exception::ITERATOR_CONSTRUCT_KEYS_NOT_IDENTICAL
            );
        }
        $this->keys      = $keys;
        $this->current   = 0;
        $this->domainMap = $domainMap;
        $this->rangeMap  = $rangeMap;
    }

    // IteratorInterface //

    public function current()
    {
        return $this->rangeMap[$this->keys[$this->current]];
    }

    public function key()
    {
        return $this->domainMap[$this->keys[$this->current]];
    }

    public function next()
    {
        $this->current++;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function valid()
    {
        return isset($this->keys[$this->current]);
    }
}
