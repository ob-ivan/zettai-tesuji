<?php
namespace Ob_Ivan\EviType;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const NOT_IMPLEMENTED_YET                           = 0x010101;

    const TYPE_CALL_NAME_UNKNOWN                        = 0x010201;
    const TYPE_CALL_VALUE_METHOD_NAME_UNKNOWN           = 0x010202;
    const TYPE_FROM_ANY_FAIL                            = 0x010203;
    const TYPE_FROM_IMPORT_FAIL                         = 0x010204;
    const TYPE_FROM_VALUE_WRONG_TYPE                    = 0x010205;
    const TYPE_GET_NAME_UNKNOWN                         = 0x010206;
    const TYPE_TO_EXPORT_NAME_UNKNOWN                   = 0x010207;

    const TYPE_FACTORY_PRODUCE_NAME_UNKNOWN             = 0x010301;
    const TYPE_FACTORY_PRODUCE_SORT_WRONG_TYPE          = 0x010302;
    const TYPE_FACTORY_REGISTRY_NAME_ALREADY_EXISTS     = 0x010303;

    const TYPE_SERVICE_OFFSET_GET_OFFSET_UNKNOWN        = 0x010401;
    const TYPE_SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS   = 0x010402;
    const TYPE_SERVICE_OFFSET_SET_VALUE_WRONG_TYPE      = 0x010403;
    const TYPE_SERVICE_OFFSET_UNSET_UNSUPPORTED         = 0x010404;
    const TYPE_SERVICE_REGISTER_NAME_ALREADY_EXISTS     = 0x010405;

    const VALUE_OFFSET_EXISTS_NOT_SUPPORTED             = 0x010501;
    const VALUE_OFFSET_GET_NOT_SUPPORTED                = 0x010502;
    const VALUE_OFFSET_SET_NOT_ALLOWED                  = 0x010503;
    const VALUE_OFFSET_UNSET_NOT_ALLOWED                = 0x010504;

    const VIEW_FACTORY_PRODUCE_NAME_UNKNOWN             = 0x010601;
    const VIEW_FACTORY_PRODUCE_SORT_WRONG_TYPE          = 0x010602;
    const VIEW_FACTORY_REGISTRY_NAME_ALREADY_EXISTS     = 0x010603;

    const VIEW_SERVICE_CALL_NAME_UNKNOWN                = 0x010701;
    const VIEW_SERVICE_OFFSET_GET_OFFSET_UNKNOWN        = 0x010702;
    const VIEW_SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS   = 0x010703;
    const VIEW_SERVICE_OFFSET_UNSET_UNSUPPORTED         = 0x010704;
    const VIEW_SERVICE_REGISTER_NAME_ALREADY_EXISTS     = 0x010705;
    const VIEW_SERVICE_SET_VALUE_WRONG_TYPE             = 0x010706;
}
