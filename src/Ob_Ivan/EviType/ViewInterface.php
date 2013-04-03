<?php
namespace Ob_Ivan\EviType;

interface ViewInterface
{
    /**
     * Пытается превратить строковое представление в объект значения.
     *
     *  @param  string  $presentation
     *  @return Value | null
    **/
    public function from($presentation);

    /**
     * Формирует строковое представление по внутреннему значению.
     *
     *  @param  mixed   $internal
     *  @return string
    **/
    public function to($internal);
}
