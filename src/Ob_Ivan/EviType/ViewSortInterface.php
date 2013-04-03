<?php
namespace Ob_Ivan\EviType;

interface ViewSortInterface
{
    /**
     * Возвращает производитель представления с заданными параметрами.
     *
     *  @param  array           $arguments
     *  @return ViewInterface()
    **/
    public function produce(array $arguments);

    /**
     * Превращает строковое представление в объект значения
     * по параметрам представления.
     *
     *  @param  array   $arguments
     *  @param  string  $presentation
     *  @return Value
    **/
    public function from(array $arguments, $presentation);

    /**
     * Превращает внутреннее значение в строковое представление
     * по параметрам представления.
     *
     *  @param  array   $arguments
     *  @param  mixed   $internal
     *  @return string
    **/
    public function to(array $arguments, $internal);
}
