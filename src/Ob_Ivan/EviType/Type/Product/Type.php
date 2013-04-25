<?php
namespace Ob_Ivan\EviType\Type\Product;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Type\IterableInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface
{
    // public : IterableInterface //

    /**
     * Перебирает все возможные сочетания из значений своих компонент.
    **/
    public function each()
    {
        /**
         * Счётчик значений для каждой компоненты.
         *
         *  @var [<index componentName> => <integer index>]
        **/
        $indexes = [];

        /**
         * Набор значений для каждой компоненты.
         *
         *  @var [<index componentName> => [<integer index> => <Value value>]]
        **/
        $values = [];

        /**
         * Количество значений в каждой компоненте.
         *
         *  @var [<index componentName> => count(values[componentName])]
        **/
        $counts = [];

        /**
         * Общее количество значений.
         *
         *  @var integer Product(i, counts[i])
        **/
        $totalCount = 1;

        // TODO
    }

    // public : ParentType //

    public function __construct(OptionsInterface $options = null)
    {
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be instance of Options',
                Exception::TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE
            );
        }
        parent::__construct($options);
    }

    public function callValueMethod(InternalInterface $internal, $name, array $arguments)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be instance of Internal',
                Exception::TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE
            );
        }
        return parent::callValueMethod($internal, $name, $arguments);
    }

    // public : Type : view factory //

    public function concat($map)
    {
        return new View\Separator('', $map);
    }

    public function separator($separator, $map)
    {
        return new View\Separator($separator, $map);
    }
}
