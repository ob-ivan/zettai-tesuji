<?php
namespace Ob_Ivan\EviType\Type\Union;

use Ob_Ivan\EviType\Type\Exception as ParentException;

class Exception extends ParentException
{
    const OPTIONS_CONSTRUCT_TYPE_WRONG_TYPE             = 0x030101;
    const OPTIONS_OFFSET_SET_PROHIBITED                 = 0x030102;
    const OPTIONS_OFFSET_UNSET_PROHIBITED               = 0x030103;

    const TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE             = 0x030201;
    const TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE    = 0x030202;
    const TYPE_EACH_VARIANT_NOT_ITERABLE                = 0x030203;
}
