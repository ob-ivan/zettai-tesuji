<?php
namespace Ob_Ivan\EviType\Type\Product;

use Ob_Ivan\EviType\Type\Exception as ParentException;

class Exception extends ParentException
{
    const INTERNAL_CONSTRUCT_VALUE_WRONG_TYPE           = 0x030101;
    const INTERNAL_OFFSET_SET_PROHIBITED                = 0x030102;
    const INTERNAL_OFFSET_UNSET_PROHIBITED              = 0x030103;

    const OPTIONS_CONSTRUCT_TYPE_WRONG_TYPE             = 0x030201;
    const OPTIONS_OFFSET_SET_PROHIBITED                 = 0x030202;
    const OPTIONS_OFFSET_UNSET_PROHIBITED               = 0x030203;

    const TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE    = 0x030301;
    const TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE             = 0x030302;
    const TYPE_EACH_COMPONENT_NOT_ITERABLE              = 0x030303;
}
