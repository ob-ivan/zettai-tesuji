<?php
namespace Ob_Ivan\EviType\ViewSort\Product;

use Ob_Ivan\EviType\View;
use Ob_Ivan\EviType\ViewSortInterface;

class Separator implements ViewSortInterface
{
    /**
     * Возвращает проивзодитель представления.
     *
     *  @param  array   $arguments
     *  @return View()
    **/
    public function produce(array $arguments)
    {
        return function () use ($arguments) { return new View($this, $arguments); };
    }
}
