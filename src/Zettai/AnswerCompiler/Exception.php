<?php
namespace Zettai\AnswerCompiler;

use Zettai\Exception as VendorException;

class Exception extends VendorException
{
    const TYPE_UNKNOWN      = 0x0201;
    const GET_UNKNOWN_FIELD = 0x0202;
}
