<?php
/**
 * Представление строкового типа как строки.
**/
namespace Ob_Ivan\EviType\Sort\String\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\String\Internal;

class String implements ViewInterface
{
    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::STRING_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return strval($internal->getPrimitive());
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        return new Internal(strval($presentation));
    }
}
