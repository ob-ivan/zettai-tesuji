<?php
namespace Ob_Ivan\EviType\Sort\Integer;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Sort\Integer\Type,
    Ob_Ivan\EviType\Sort\Integer\View;

class Builder implements BuilderInterface
{
    /**
     * Строит целочисленный тип и наполняет его стандартными представлениями.
     *
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $type = new Type();
        $type->view('integer', new View\Integer);
        $type->view('string',  new View\String);
        return $type;
    }
}
