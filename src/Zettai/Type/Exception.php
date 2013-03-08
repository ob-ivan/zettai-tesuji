<?php
namespace Zettai\Type;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_SET_OFFSET_ALREADY_EXISTS     = 0x020101;
    const SERVICE_SET_VALUE_WRONG_TYPE          = 0x020102;
    const SERVICE_UNSET_UNSUPPORTED             = 0x020103;
    
    const ENUM_CALL_METHOD_UNKNOWN              = 0x020201;
    const ENUM_FROM_VIEW_ARGUMENT_ABSENT        = 0x020202;
    const ENUM_FROM_VIEW_UNKNOWN_VIEW           = 0x020203;
    const ENUM_TO_VIEW_NAME_UNKNOWN             = 0x020204;
    const ENUM_TO_VIEW_UNSUPPORTED_VIEW         = 0x020205;
    const ENUM_TO_VIEW_UNSUPPORTED_PRIMITIVE    = 0x020206;
    
    const VALUE_CALL_METHOD_UNKNOWN             = 0x020301;
}
