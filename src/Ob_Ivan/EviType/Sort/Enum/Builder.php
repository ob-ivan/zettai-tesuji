<?php
namespace Ob_Ivan\EviType\Sort\Enum;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * Строит перечислимый тип из переданного массива.
     *
     *  @param  array   $arguments = [
     *      0 => [
     *          <primitive> => <name>,
     *          ...
     *      ]
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $map = [];
        foreach ($arguments[0] as $primitive => $name) {
            $map[$primitive] = strval($name);
        }
        $options = new Options($map);
        $type = new Type($options);
        $type->view('Default', $type->dictionary($options));
        return $type;
    }
}
