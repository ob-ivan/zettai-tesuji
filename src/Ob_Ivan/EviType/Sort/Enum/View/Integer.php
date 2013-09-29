<?php
namespace Ob_Ivan\EviType\Sort\Enum\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Enum\Internal;

class Integer implements ViewInterface
{
    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::DICTIONARY_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return intval($options[$internal->getPrimitive()]);
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        $presentation = intval($presentation);
        foreach ($options as $primitive => $name) {
            if ($presentation === intval($name)) {
                return new Internal($primitive);
            }
        }
    }
}
