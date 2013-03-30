<?php
namespace Zettai\AnswerCompiler;

use Zettai\Exception as VendorException;

class Exception extends VendorException
{
    const SERVICE_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS = 0x0201;
    const TOKEN_GET_NAME_UNKNOWN                        = 0x0202;
}
