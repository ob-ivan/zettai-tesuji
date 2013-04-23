<?php
/**
 * Представление произведения типов как запись через разделитель.
 *
 * Для каждой компоненты указывается, какое представление на ней используется.
**/
namespace Ob_Ivan\EviType\Type\Product\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\Product\Internal;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\ViewInterface;

class Separator implements ViewInterface
{
    private $map;
    private $separator;

    /**
     *  @param  string                          $separator
     *  @param  [<componentName> => <viewName>] $map
    **/
    public function __construct($separator, array $map)
    {
        if (! (is_array($map) || ($map instanceof ArrayAccess && $map instanceof Traversable))) {
            throw new Exception(
                'Map must be an array or implement array-like behaviour',
                Exception::SEPARATOR_CONSTRUCT_MAP_WRONG_TYPE
            );
        }
        $this->map          = $map;
        $this->separator    = $separator;
    }

    public function export(InternalInterface $internal)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::SEPARATOR_EXPORT_INTERNAL_WRONG_TYPE
            );
        }

        $presentations = [];
        foreach ($internal as $componentName => $value) {
            $presentations[] = $value->{'to' . $this->map[$componentName]}();
        }
        return implode($this->separator, $presentations);
    }

    public function import($presentation)
    {
        throw new Exception('Not implemented yet', Exception::NOT_IMPLEMENTED_YET);
    }
}
