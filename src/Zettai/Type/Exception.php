<?php
namespace Zettai\Type;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const VALUE_CALL_METHOD_UNKNOWN = 0x020101;
    const VALUE_PROJECT_UNSUPPORTED = 0x020102;
    const VALUE_SET_UNSUPPORTED     = 0x020103;
    const VALUE_UNSET_UNSUPPORTED   = 0x020104;
}
