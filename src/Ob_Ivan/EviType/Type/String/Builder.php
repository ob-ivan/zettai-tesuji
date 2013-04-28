<?php
namespace Ob_Ivan\EviType\Type\String;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Type\String\Type,
    Ob_Ivan\EviType\Type\String\View;

class Builder implements BuilderInterface
{
    /**
     * Строит строковый тип и наполняет его стандартными представлениями.
     *
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $type = new Type();
        $type->view('string', new View\String);
        return $type;
    }
}
