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
            $map[$primitive] = $name;
        }
        $options = new Options($map);
        $type = new Type($options);
        $type->view('default', $type->dictionary($options));
        $type->view('integer', $type->integer());
        return $type;
    }
}
