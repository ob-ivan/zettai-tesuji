<?php
namespace Zettai\Type\Type;

use Zettai\Type\Exception as ParentException;

class Exception extends ParentException
{
    const ENUM_TO_VIEW_UNSUPPORTED_PRIMITIVE        = 0x030101;
    
    const PRODUCT_DEREFERENCE_OFFSET_UNKNOWN        = 0x030201;
    
    const SERVICE_REGISTER_NAME_ALREADY_EXISTS      = 0x030301;
    const SERVICE_SET_OFFSET_ALREADY_EXISTS         = 0x030302;
    const SERVICE_SET_VALUE_WRONG_TYPE              = 0x030303;
    const SERVICE_UNSET_UNSUPPORTED                 = 0x030304;
    
    const TYPE_CALL_METHOD_UNKNOWN                  = 0x030401;
    const TYPE_FROM_VIEW_ARGUMENT_ABSENT            = 0x030402;
    const TYPE_FROM_VIEW_UNKNOWN_VIEW               = 0x030403;
    const TYPE_TO_VIEW_NAME_UNKNOWN                 = 0x030404;
    
    const VIEWABLE_TO_VIEW_NAME_UNKNOWN             = 0x030501;
    const VIEWABLE_TO_VIEW_UNSUPPORTED_VIEW         = 0x030502;
    const VIEWABLE_TO_VIEW_UNSUPPORTED_PRIMITIVE    = 0x030503;
}
