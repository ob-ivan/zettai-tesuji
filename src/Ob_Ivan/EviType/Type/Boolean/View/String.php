<?php
/**
 * Представление булевого типа как строки.
**/
namespace Ob_Ivan\EviType\Type\Boolean\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Type\Boolean\Internal;

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
        if ($internal->getPrimitive() === true) {
            return 'true';
        }
        if ($internal->getPrimitive() === false) {
            return 'false';
        }
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        switch ($presentation) {
            case 'true' : return new Internal(true);
            case 'false': return new Internal(false);
        }
    }
}
