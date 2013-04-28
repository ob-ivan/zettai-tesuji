<?php
/**
 * Представление целочисленного типа как целого числа.
**/
namespace Ob_Ivan\EviType\Type\Integer\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Type\Integer\Internal;

class Integer implements ViewInterface
{
    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::STRING_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return intval($internal->getPrimitive());
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        return new Internal(intval($presentation));
    }
}
