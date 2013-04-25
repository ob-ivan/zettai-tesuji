<?php
namespace Ob_Ivan\EviType\Type\Product;

use Ob_Ivan\EviType\InternalInterface;
use Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Type as ParentType;

class Type extends ParentType
{
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
        // TODO
    }

    // public : view factory //

    public function concat($map)
    {
        return new View\Separator('', $map);
    }

    public function separator($separator, $map)
    {
        return new View\Separator($separator, $map);
    }
}