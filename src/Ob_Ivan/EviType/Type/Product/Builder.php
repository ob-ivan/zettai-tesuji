<?php
namespace Ob_Ivan\EviType\Type\Product;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * Строит тип-произведение из переданного массива.
     *
     *  @param  array   $arguments = [
     *      <componentName> => <TypeInterace type>,
     *      ...
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments)
    {
        $options = new Options($arguments);
        $type = new Type($options);
        return $type;
    }
}
