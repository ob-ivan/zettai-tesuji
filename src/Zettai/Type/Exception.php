<?php
namespace Zettai\Type;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const ENUM_TO_VIEW_UNSUPPORTED_PRIMITIVE        = 0x020101;
    
    const PRODUCT_PROJECT_COORDINATE_UNKNOWN        = 0x020201;
    
    const SERVICE_REGISTER_NAME_ALREADY_EXISTS      = 0x020301;
    const SERVICE_SET_OFFSET_ALREADY_EXISTS         = 0x020302;
    const SERVICE_SET_VALUE_WRONG_TYPE              = 0x020303;
    const SERVICE_UNSET_UNSUPPORTED                 = 0x020304;
    
    const TYPE_CALL_METHOD_UNKNOWN                  = 0x020401;
    const TYPE_FROM_VIEW_ARGUMENT_ABSENT            = 0x020402;
    const TYPE_FROM_VIEW_UNKNOWN_VIEW               = 0x020403;
    
    const VALUE_CALL_METHOD_UNKNOWN                 = 0x020501;
    const VALUE_PROJECT_UNSUPPORTED                 = 0x020502;
    
    const VIEWABLE_TO_VIEW_NAME_UNKNOWN             = 0x020601;
    const VIEWABLE_TO_VIEW_UNSUPPORTED_VIEW         = 0x020602;
    const VIEWABLE_TO_VIEW_UNSUPPORTED_PRIMITIVE    = 0x020603;
}
