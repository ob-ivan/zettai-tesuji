<?php
namespace Ob_Ivan\EviType;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const TYPE_GET_NAME_UNKNOWN                         = 0x010101;

    const TYPE_FACTORY_PRODUCE_NAME_UNKNOWN             = 0x010201;
    const TYPE_FACTORY_PRODUCE_SORT_WRONG_TYPE          = 0x010202;
    const TYPE_FACTORY_REGISTRY_NAME_ALREADY_EXISTS     = 0x010203;

    const TYPE_SERVICE_OFFSET_GET_OFFSET_UNKNOWN        = 0x010301;
    const TYPE_SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS   = 0x010302;
    const TYPE_SERVICE_OFFSET_SET_VALUE_WRONG_TYPE      = 0x010303;
    const TYPE_SERVICE_OFFSET_UNSET_UNSUPPORTED         = 0x010304;
    const TYPE_SERVICE_REGISTER_NAME_ALREADY_EXISTS     = 0x010305;

    const VIEW_FACTORY_PRODUCE_NAME_UNKNOWN             = 0x010401;
    const VIEW_FACTORY_PRODUCE_SORT_WRONG_TYPE          = 0x010402;
    const VIEW_FACTORY_REGISTRY_NAME_ALREADY_EXISTS     = 0x010403;

    const VIEW_SERVICE_CALL_NAME_UNKNOWN                = 0x010501;
}
