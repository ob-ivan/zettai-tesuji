<?php
/**
 * Интерфейс для типов, значения которых можно передавать в foreach.
**/
namespace Ob_Ivan\EviType\Sort;

use Ob_Ivan\EviType\InternalInterface;

interface ValueIteratorInterface
{
    /**
     * Возвращает итератор по элементам внутреннего значения.
     *
     *  @return Iterator
    **/
    public function getValueIterator(InternalInterface $internal);
}
