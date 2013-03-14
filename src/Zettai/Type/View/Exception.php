<?php
namespace Zettai\Type\View;

use Zettai\Type\Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_REGISTER_NAME_ALREADY_EXISTS  = 0x030101;
    const SERVICE_SET_OFFSET_ALREADY_EXISTS     = 0x030102;
    const SERVICE_SET_VALUE_WRONG_TYPE          = 0x030103;
    const SERVICE_UNSET_NOT_SUPPORTED           = 0x030104;
}
