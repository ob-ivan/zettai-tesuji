<?php
namespace Zettai\Model;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_GET_NAME_UNKNOWN          = 0x0201;
    const EXERCISE_ID_EMPTY                 = 0x0202;
    const EXERCISE_TITLE_EMPTY              = 0x0203;
    const EXERCISE_CONTENT_EMPTY            = 0x0204;
    const EXPRESSION_CALL_NAME_UNKNOWN      = 0x0205;
    const EXPRESSION_TO_STRING_TYPE_UNKNOWN = 0x0206;
}