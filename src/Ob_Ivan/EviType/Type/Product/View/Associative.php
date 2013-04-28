<?php
/**
 * Представление произведения типов как массива.
 *
 * Для каждой компоненты указывается, какое представление на ней используется.
**/
namespace Ob_Ivan\EviType\Type\Product\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Type\Product\Internal,
    Ob_Ivan\EviType\Type\Product\Options;

class Associative implements ViewInterface
{
    private $map;

    /**
     *  @param  [<componentName> => <viewName>] $map
    **/
    public function __construct(array $map)
    {
        if (! (is_array($map) || ($map instanceof ArrayAccess && $map instanceof Traversable))) {
            throw new Exception(
                'Map must be an array or implement array-like behaviour',
                Exception::ASSOCIATIVE_CONSTRUCT_MAP_WRONG_TYPE
            );
        }
        $this->map = $map;
    }

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::ASSOCIATIVE_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        $presentations = [];
        foreach ($internal as $componentName => $value) {
            $presentations[$componentName] = $value->to($this->map[$componentName]);
        }
        return $presentations;
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        if (! (is_array($presentation) || ($presentation instanceof ArrayAccess && $presentation instanceof Traversable))) {
            throw new Exception(
                'Presentation must be an array or implement array-like behaviour',
                Exception::ASSOCIATIVE_IMPORT_PRESENTATION_WRONG_TYPE
            );
        }
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be an instance of Options, ' . get_class($options) . ' given',
                Exception::ASSOCIATIVE_IMPORT_OPTIONS_WRONG_TYPE
            );
        }
        $values = [];
        foreach ($options as $componentName => $type) {
            if (! isset($presentation[$componentName])) {
                return null;
            }
            $value = $type->from($this->map[$componentName], $presentation[$componentName]);
            if (! $value) {
                return null;
            }
            $values[$componentName] = $value;
        }
        return new Internal($values);
    }
}
