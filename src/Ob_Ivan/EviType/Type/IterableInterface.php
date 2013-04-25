<?php
/**
 * Интерфейс для типов, значения которых можно перебрать методом each.
**/
namespace Ob_Ivan\EviType\Type;

interface IterableInterface
{
    /**
     * Возвращает массив всех возможных значений типа.
     *
     *  @return [Value]
    **/
    public function each();
}
