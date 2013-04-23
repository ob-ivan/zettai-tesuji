<?php
namespace Ob_Ivan\EviType\Type\Enum;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    public function produce(array $arguments)
    {
        $options = new Options($arguments);
        $type = new Type($options);

        // TODO: Объединить во view.
        $type->export('Default', function (Internal $internal, Options $options) {
            return $options[$internal->getPrimitive()];
        });
        $type->import('Default', function ($presentation, Options $options) {
            foreach ($options as $primitive => $name) {
                if ($presentation === $name) {
                    return new Internal($primitive);
                }
            }
        });

        return $type;
    }
}
