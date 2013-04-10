<?php
namespace Ob_Ivan\Model;

use Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_GET_NAME_UNKNOWN          = 0x0101;
    const EXERCISE_ID_EMPTY                 = 0x0102;
    const EXERCISE_TITLE_EMPTY              = 0x0103;
    const EXERCISE_CONTENT_EMPTY            = 0x0104;
    const EXPRESSION_CALL_NAME_UNKNOWN      = 0x0105;
    const EXPRESSION_TO_STRING_TYPE_UNKNOWN = 0x0106;
}
