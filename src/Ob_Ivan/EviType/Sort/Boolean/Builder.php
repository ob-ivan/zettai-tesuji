<?php
namespace Ob_Ivan\EviType\Sort\Boolean;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Sort\Boolean\Type,
    Ob_Ivan\EviType\Sort\Boolean\View;

class Builder implements BuilderInterface
{
    /**
     * Строит булевый тип и наполняет его стандартными представлениями.
     *
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $type = new Type();
        $type->view('boolean', new View\Boolean);
        $type->view('integer', new View\Integer);
        $type->view('string',  new View\String);
        return $type;
    }
}
