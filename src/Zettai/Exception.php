<?php
namespace Zettai;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const ARRAY_OBJECT_READ_ONLY        = 0x0101;
    const CONFIG_READ_ONLY              = 0x0102;
    const CONFIG_VARIABLE_UNKNOWN       = 0x0103;
    const MODEL_EXERCISE_ID_EMPTY         = 0x0104;
    const MODEL_EXERCISE_TITLE_EMPTY      = 0x0105;
    const MODEL_EXERCISE_CONTENT_EMPTY    = 0x0106;
    const EXERCISE_GET_FIELD_UNKNOWN      = 0x0107;
    const EXERCISE_TYPE_UNKNOWN           = 0x0108;
    const EXERCISE_JSON_NOT_ARRAY         = 0x0109;
}
