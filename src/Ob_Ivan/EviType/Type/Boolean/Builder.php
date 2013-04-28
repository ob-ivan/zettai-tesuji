<?php
namespace Ob_Ivan\EviType\Type\Boolean;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Type\Boolean\Type,
    Ob_Ivan\EviType\Type\Boolean\View;

class Builder implements BuilderInterface
{
    /**
     * Строит булевый тип и наполняет его стандартными представлениями.
     *
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $type = new Type($options);
        $type->view('string',  new View\String);
        $type->view('integer', new View\Integer);
        return $type;
    }
}
