<?php
namespace Ob_Ivan\EviType\Sort\Product;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\IterableInterface,
    Ob_Ivan\EviType\Sort\ValueIteratorInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface, ValueIteratorInterface
{
    // var //

    private $each = null;

    // public : IterableInterface //

    /**
     * Перебирает все возможные сочетания из значений своих компонент.
    **/
    public function each()
    {
        if (is_null($this->each)) {
            $this->each = $this->generateEach();
        }
        return $this->each;
    }

    // public : ValueIteratorInterface //

    public function getValueIterator(InternalInterface $internal)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be instance of Internal',
                Exception::TYPE_GET_VALUE_ITERATOR_INTERNAL_WRONG_TYPE
            );
        }
        return $internal->getIterator();
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

        // Наделить значения возможностью выбирать значения координат.
        $this->getter('__get', function ($name, Internal $internal) {
            return $internal[$name];
        });
        $this->getter('__isset', function ($name, Internal $internal) {
            return isset($internal[$name]);
        });
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

    public function associative($map)
    {
        return new View\Associative($map);
    }

    public function concat($map)
    {
        return new View\Separator('', $map);
    }

    public function json($map)
    {
        return new View\Json($map);
    }

    public function separator($separator, $map)
    {
        return new View\Separator($separator, $map);
    }

    // private //

    private function generateEach()
    {
        /**
         * Перечисление компонент для формирования на них порядка
         * инкремента и переброса переполнения.
         *
         *  @var [<index componentIndex> => <index componentName>]
        **/
        $components = [];

        /**
         * Счётчик значений для каждой компоненты.
         *
         *  @var [<index componentIndex> => <integer indexValue>]
        **/
        $indexes = [];

        /**
         * Набор значений для каждой компоненты.
         *
         *  @var [<index componentIndex> => [<integer indexValue> => <Value value>]]
        **/
        $values = [];

        /**
         * Количество значений в каждой компоненте.
         *
         *  @var [<index componentIndex> => count(values[componentIndex])]
        **/
        $counts = [];

        /**
         * Общее количество значений.
         *
         *  @var integer Product(i, counts[i])
        **/
        $totalCount = 1;

        foreach ($this->getOptions() as $componentName => $type) {
            if (! $type instanceof IterableInterface) {
                throw new Exception(
                    'Component "' . $componentName . '" cannot be iterated',
                    Exception::TYPE_EACH_COMPONENT_NOT_ITERABLE
                );
            }
            $componentIndex = count($components);
            $components [$componentIndex] = $componentName;
            $indexes    [$componentIndex] = 0;
            $values     [$componentIndex] = $type->each();
            $counts     [$componentIndex] = count($values[$componentIndex]);
            $totalCount                   *= $counts[$componentIndex];
        }

        $each = [];
        for ($i = 0; $i < $totalCount; ++$i) {
            // Породить отображение на основе индексов.
            foreach ($indexes as $componentIndex => $index) {
                $map[$components[$componentIndex]] = $values[$componentIndex][$index];
            }

            // Породить значение из отображения.
            $each[] = $this->produceValue(new Internal($map));

            // Увеличить индексы на единицу в младшем разряде
            // и при необходимости перекинуть переполнение дальше.
            $indexes = $this->incrementIndexes($indexes, $counts);
        }
        return $each;
    }

    private function incrementIndexes(array $indexes, array $counts)
    {
        for ($i = count($indexes) - 1; $i >= 0; --$i) {
            ++$indexes[$i];
            if ($indexes[$i] < $counts[$i]) {
                return $indexes;
            }
            $indexes[$i] = 0;
        }
        return $indexes;
    }
}
