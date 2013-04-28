<?php
namespace Ob_Ivan\EviType\Type\Product\Builder;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Type\Product\Options,
    Ob_Ivan\EviType\Type\Product\Type;

class Cartesian implements BuilderInterface
{
    /**
     * Строит тип-произведение из переданного массива.
     *
     *  @param  array   $arguments = [
     *      <TypeInterace type>,
     *      ...
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $options = new Options($arguments);
        $type = new Type($options);
        return $type;
    }
}
