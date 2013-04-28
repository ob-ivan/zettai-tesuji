<?php
/**
 * Представление произведения типов как запись через разделитель.
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

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::SEPARATOR_EXPORT_INTERNAL_WRONG_TYPE
            );
        }

        $presentations = [];
        foreach ($internal as $componentName => $value) {
            $presentations[] = $value->to($this->map[$componentName]);
        }
        return implode($this->separator, $presentations);
    }

    /**
     * Разбирает строку с разделителями, чтобы получить значения отдельных координат.
     *
     * Для каждой координаты пытается получить значение. При этом поскольку
     * строка для разбора одного значения может содержать в себе разделители,
     * то если кусок не разбирается, добавляем следующие куски до тех пор,
     * пока не разберётся.
     * Если так и не разобралось, а вход закончился, то вход нам не подходит.
     * Если разобрано, а вход не закончился, то он тоже не подходит.
     *
     *  @param  string  $presentation
     *  @param  Options $options
     *  @return Internal
    **/
    public function import($presentation, OptionsInterface $options = null)
    {
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be an instance of Options, ' . get_class($options) . ' given',
                Exception::SEPARATOR_IMPORT_OPTIONS_WRONG_TYPE
            );
        }
        $presentations = $this->explode($presentation);
        $valueMap = [];
        $current = array_shift($presentations);
        foreach ($options as $componentName => $type) {
            while (true) {
                $value = $type->from($this->map[$componentName], $current);
                if (! $value) {
                    if (empty($presentations)) {
                        return null;
                    }
                    $current .= $this->separator . array_shift($presentations);
                } else {
                    $valueMap[$componentName] = $value;
                    if (! empty($presentations)) {
                        $current = array_shift($presentations);
                    }
                    break;
                }
            }
        }
        // Если все координаты прочитаны, а что-то ещё осталось, значит, вход неправильный.
        if (! empty($presentations)) {
            return null;
        }
        return new Internal($valueMap);
    }

    // private //

    private function explode($string)
    {
        if (strlen($this->separator)) {
            return explode($this->separator, $string);
        }
        return str_split($string);
    }
}
