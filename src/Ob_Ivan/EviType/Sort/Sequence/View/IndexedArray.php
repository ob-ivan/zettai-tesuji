<?php
namespace Ob_Ivan\EviType\Sort\Sequence\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\Value,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Sequence\Internal,
    Ob_Ivan\EviType\Sort\Sequence\Options;

class IndexedArray implements ViewInterface
{
    private $viewName;

    public function __construct($viewName)
    {
        $this->viewName = $viewName;
    }

    // public : ViewInterface //

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::INDEXED_ARRAY_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        $array = [];
        foreach ($internal as $index => $value)
        {
            $array[$index] = $this->viewName === '*'
                ? $value
                : $value->to($this->viewName);
        }
        return $array;
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        if (! (is_array($presentation) || ($presentation instanceof ArrayAccess && $presentation instanceof Traversable))) {
            throw new Exception(
                'Presentation must be an array or implement array-like behaviour',
                Exception::INDEXED_ARRAY_IMPORT_PRESENTATION_WRONG_TYPE
            );
        }
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be an instance of Options, ' . get_class($options) . ' given',
                Exception::INDEXED_ARRAY_IMPORT_OPTIONS_WRONG_TYPE
            );
        }
        $values = [];
        $type = $options->getType();
        foreach ($presentation as $index => $subpresentation) {
            $value = $this->viewName === '*'
                ? $type->fromAny($subpresentation)
                : $type->from($this->viewName, $subpresentation);
            if (! $value) {
                return null;
            }
            $values[$index] = $value;
        }
        return new Internal($values);
    }
}
