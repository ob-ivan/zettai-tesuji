<?php
namespace Zettai\Type;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const SERVICE_SET_OFFSET_ALREADY_EXISTS = 0x0201;
    const SERVICE_SET_VALUE_WRONG_TYPE      = 0x0202;
    const SERVICE_UNSET_UNSUPPORTED         = 0x0203;
}
