<?php
namespace Zettai;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const ARRAY_OBJECT_READ_ONLY        = 0x010101;

    const CONFIG_READ_ONLY              = 0x010201;
    const CONFIG_VARIABLE_UNKNOWN       = 0x010202;

    const EXERCISE_DEPRECATED           = 0x010301;
    const EXERCISE_GET_FIELD_UNKNOWN    = 0x010302;
    const EXERCISE_MODIFY_FIELD_UNKNOWN = 0x010303;
    const EXERCISE_TYPE_UNKNOWN         = 0x010304;
    const EXERCISE_JSON_NOT_ARRAY       = 0x010305;

    const TILE_DEPRECATED               = 0x010401;
}
