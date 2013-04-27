<?php
namespace Ob_Ivan\EviType;

interface BuilderInterface
{
    /**
     * Возвращает новый тип, характеризуемый заданыыми параметрами,
     * с заполненными дефолтными представлениями.
     *
     *  @param  array           $arguments
     *  @return TypeInterface
    **/
    public function produce(array $arguments);
}
