<?php
namespace Zettai\Type;

interface ServiceInterface
{
    /**
     * Превращает имя вида в значение.
     *
     *  @param  string  $viewName
     *  @return integer
    **/
    public function getViewByName($viewName);
    
    /**
     * Возвращает список значений видов.
     *
     *  @return [integer]
    **/
    public function getViews();
}
