<?php
namespace Zettai;

use Exception as GlobalException;

class Exception extends GlobalException
{
    const CONFIG_VARIABLE_UNKNOWN = 0x0101;
}
