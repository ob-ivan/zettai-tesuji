<?php
namespace Ob_Ivan\EviType\Type\Enum;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    public function produce(array $arguments)
    {
        $options = new Options($arguments);
        $type = new Type($options);
        $type->view('Default', $type->dictionary($options));

        return $type;
    }
}
