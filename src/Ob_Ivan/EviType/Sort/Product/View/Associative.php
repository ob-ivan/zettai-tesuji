<?php
/**
 * Представление произведения типов как массива.
 *
 * Для каждой компоненты указывается, какие представления на ней используются.
 * Представление задаётся либо строкой (если достаточно одного представления),
 * либо массивом, элементы которого перебираются при каждой операции по очереди
 * вплоть до достижения успеха.
 * Представление может быть либо названием представления на типе, либо символом
 * звёздочка (*), что при импорте означает "попробовать все представления",
 * а при экспорте игнорируется.
**/
namespace Ob_Ivan\EviType\Sort\Product\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\Value,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Product\Internal,
    Ob_Ivan\EviType\Sort\Product\Options;

class Associative implements ViewInterface
{
    // var //

    /**
     *  @var [<index componentName> => [string viewName]]
    **/
    private $map = [];

    // public : ViewInterface //

    /**
     *  @param  [
     *      <index componentName> =>
     *          <string viewName | [string viewName] viewNames>,
     *      ...
     *  ]  $map
    **/
    public function __construct(array $map)
    {
        if (! (is_array($map) || ($map instanceof ArrayAccess && $map instanceof Traversable))) {
            throw new Exception(
                'Map must be an array or implement array-like behaviour',
                Exception::ASSOCIATIVE_CONSTRUCT_MAP_WRONG_TYPE
            );
        }
        foreach ($map as $componentName => $viewNames) {
            if (! is_array($viewNames)) {
                $viewNames = [$viewNames];
            }
            $this->map[$componentName] = $viewNames;
        }
    }

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::ASSOCIATIVE_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        // Построить представление для каждой компоненты.
        $presentations = [];
        foreach ($internal as $componentName => $value) {
            // Перебираем все представления компоненты вплоть до достижения успеха.
            $presentation = null;
            foreach ($this->map[$componentName] as $viewName) {
                // Представление звёздочка (*) при экспорте игнорируется.
                if ($viewName === '*') {
                    continue;
                }
                $candidate = $value->to($viewName);
                if (! is_null($candidate)) {
                    $presentation = $candidate;
                    break;
                }
            }
            $presentations[$componentName] = $presentation;
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
        // Строим значения для каждой компоненты.
        $values = [];
        foreach ($options as $componentName => $type) {
            // Компонента отсутствует, представление не может быть разобрано.
            if (! isset($presentation[$componentName])) {
                return null;
            }
            // Перебираем все представления вплоть до достижения успеха.
            $value = null;
            foreach ($this->map[$componentName] as $viewName) {
                // Звёздочка означает перебрать все представления.
                if ($viewName === '*') {
                    $value = $type->fromAny($presentation[$componentName]);
                    // После звёздочки нет смысла прогонять какие-то другие попытки.
                    break;
                }
                $candidate = $type->from($viewName, $presentation[$componentName]);
                if ($candidate instanceof Value) {
                    $value = $candidate;
                    break;
                }
            }
            if (! $value) {
                return null;
            }
            $values[$componentName] = $value;
        }
        return new Internal($values);
    }
}
