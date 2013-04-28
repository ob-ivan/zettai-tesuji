<?php
namespace Ob_Ivan\EviType\Type\Map;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * Строит тип отображения из пары типов.
     *
     *  @param  array   $arguments = [
     *      0 => <TypeInterace domain>
     *      1 => <TypeInterace range>
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $options = new Options($arguments[0], $arguments[1]);
        $type = new Type($options);
        // TODO: Добавить стандартные представления.
        return $type;
    }
}
