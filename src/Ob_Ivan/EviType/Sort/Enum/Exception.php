<?php
namespace Ob_Ivan\EviType\Sort\Enum;

use Ob_Ivan\EviType\Sort\Exception as ParentException;

class Exception extends ParentException
{
    OPTIONS_OFFSET_SET_PROHIBITED               = 0x030101;
    OPTIONS_OFFSET_UNSET_PROHIBITED             = 0x030102;

    TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE  = 0x030201;
    TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE           = 0x030202;
}
