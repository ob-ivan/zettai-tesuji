<?php
namespace Ob_Ivan\Model;

use Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_GET_NAME_UNKNOWN          = 0x0101;
    const EXPRESSION_CALL_NAME_UNKNOWN      = 0x0102;
    const EXPRESSION_TO_STRING_TYPE_UNKNOWN = 0x0103;
}
