<?php
namespace Ob_Ivan\EviType\Sort\Map;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\IterableInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType implements IterableInterface
{
    // public : IterableInterface //

    public function each()
    {
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

        // Наделить значения возможностью отбражать значения области определения в область значений.
        $this->getter('__get', function ($name, Internal $internal, Options $options) {
            return $internal[$options->getDomain()->fromAny($name)];
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

    // public : view factory //

    public function json($domainView, $rangeView)
    {
        return new View\Json($domainView, $rangeView);
    }
}
