<?php
namespace Ob_Ivan\EviType\Sort\Sequence;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * Строит тип-последовательность из переданного типа.
     *
     *  @param  array   $arguments = [
     *      0 => <TypeInterace type>
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $options = new Options($arguments[0]);
        $type = new Type($options);
        $type->view('array', new View\IndexedArray());
        return $type;
    }
}
