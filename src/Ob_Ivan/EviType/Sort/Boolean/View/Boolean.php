<?php
/**
 * Представление булевого типа как числа.
**/
namespace Ob_Ivan\EviType\Sort\Boolean\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Boolean\Internal;

class Boolean implements ViewInterface
{
    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::BOOLEAN_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return !! $internal->getPrimitive();
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        return new Internal(!! $presentation);
    }
}
