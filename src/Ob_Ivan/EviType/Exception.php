<?php
namespace Ob_Ivan\EviType;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const SERVICE_OFFSET_GET_OFFSET_UNKNOWN         = 0x010101;
    const SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS    = 0x010102;
    const SERVICE_OFFSET_SET_VALUE_WRONG_TYPE       = 0x010103;
    const SERVICE_OFFSET_UNSET_UNSUPPORTED          = 0x010104;
    const SERVICE_REGISTER_NAME_ALREADY_EXISTS      = 0x010105;
    
    const TYPE_GET_NAME_UNKNOWN                     = 0x010201;
    
    const TYPE_FACTORY_PRODUCE_NAME_UNKNOWN         = 0x010301;
    const TYPE_FACTORY_PRODUCE_SORT_WRONG_TYPE      = 0x010302;
    const TYPE_FACTORY_REGISTRY_NAME_ALREADY_EXISTS = 0x010303;
}
