<?php
namespace Ob_Ivan\EviType\ViewSort\Product;

use Ob_Ivan\EviType\View;
use Ob_Ivan\EviType\ViewSortInterface;

class Separator implements ViewSortInterface
{
    /**
     * Возвращает проивзодитель представления.
     *
     *  @param  [
     *      0 => <string separator>,
     *      1 => [<viewName>]
     *  ]   $arguments
     *  @return View
    **/
    public function produce(array $arguments)
    {
        return new View($this, $arguments);
    }

    public function from(array $arguments, $presentation)
    {
        // TODO: Получить свойства типа.
    }
}
