<?php
namespace Ob_Ivan\EviType\Type\Product;

use Ob_Ivan\EviType\Type\Exception as ParentException;

class Exception extends ParentException
{
    INTERNAL_CONSTRUCT_VALUE_WRONG_TYPE         = 0x030101;
    INTERNAL_OFFSET_SET_PROHIBITED              = 0x030102;
    INTERNAL_OFFSET_UNSET_PROHIBITED            = 0x030103;

    OPTIONS_CONSTRUCT_TYPE_WRONG_TYPE           = 0x030201;
    OPTIONS_OFFSET_SET_PROHIBITED               = 0x030202;
    OPTIONS_OFFSET_UNSET_PROHIBITED             = 0x030203;

    TYPE_CALL_VALUE_METHOD_INTERNAL_WRONG_TYPE  = 0x030301;
    TYPE_CONSTRUCT_OPTIONS_WRONG_TYPE           = 0x030302;
}
