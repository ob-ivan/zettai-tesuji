<?php
namespace Ob_Ivan\EviType\ViewSort\Enum;

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
    public function produce(array $arguments)
    {
        return function () use ($arguments) { return new View($this, $arguments); };
    }

    public function from(array $arguments, $presentation)
    {
        foreach ($arguments as $internal => $candidate) {
            if ($candidate === $presentation) {
                return $internal;
            }
        }
        return null;
    }

    public function to(array $arguments, $internal)
    {
        if (isset($arguments[$internal])) {
            return $arguments[$internal];
        }
        return null;
    }
}
