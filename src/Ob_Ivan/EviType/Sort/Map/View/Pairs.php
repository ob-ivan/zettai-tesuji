<?php
namespace Ob_Ivan\EviType\Sort\Map\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\Value,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Map\Internal,
    Ob_Ivan\EviType\Sort\Map\Options;

class Pairs implements ViewInterface
{
    // public : ViewInterface //

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::PAIRS_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        $pairs = [];
        foreach ($internal as $key => $value) {
            $pairs[] = [$key, $value];
        }
        return $pairs;
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        if (! (is_array($presentation) || ($presentation instanceof ArrayAccess && $presentation instanceof Traversable))) {
            throw new Exception(
                'Presentation must be an array or implement array-like behaviour',
                Exception::PAIRS_IMPORT_PRESENTATION_WRONG_TYPE
            );
        }
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be an instance of Options, ' . get_class($options) . ' given',
                Exception::PAIRS_IMPORT_OPTIONS_WRONG_TYPE
            );
        }
        $pairs = [];
        $domain = $options->getDomain();
        $range  = $options->getRange();
        foreach ($presentation as $key => $value) {
            $domainValue = $domain->fromAny($key);
            if (! $domainValue) {
                throw new Exception(
                    'Key "' . $key . '" could not be converted to domain value',
                    Exception::PAIRS_IMPORT_KEY_NOT_RECOGNIZED
                );
            }
            $rangeValue = $range->fromAny($value);
            if (! $rangeValue) {
                throw new Exception(
                    'Value could not be converted to range value',
                    Exception::PAIRS_IMPORT_VALUE_NOT_RECOGNIZED
                );
            }
            $pairs[] = [$domainValue, $rangeValue];
        }
        return new Internal($pairs);
    }
}
