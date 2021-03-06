<?php
namespace Ob_Ivan\EviType\Sort\Sequence;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\ValueIteratorInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements ValueIteratorInterface
{
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

    // public : view factory //

    public function indexedArray($viewName)
    {
        return new View\IndexedArray($viewName);
    }
}
