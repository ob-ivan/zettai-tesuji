<?php
namespace Ob_Ivan\EviType\ViewSort;

use Ob_Ivan\EviType\View;
use Ob_Ivan\EviType\ViewSortInterface;

class Dictionary implements ViewSortInterface
{
    /**
     * Возвращает проивзодитель представления.
     *
     *  @param  array   $arguments
     *  @return View()
    **/
    public function produce($arguments)
    {
        return function () use ($arguments) { return new View($arguments); };
    }
}
