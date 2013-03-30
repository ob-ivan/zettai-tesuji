<?php
namespace Zettai\AnswerCompiler;

use Zettai\Exception as VendorException;

class Exception extends VendorException
{
    const LEXER_CONSUME_TOKEN_POSITION_WRONG            = 0x0201;
    const SERVICE_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS = 0x0202;
    const TOKEN_GET_NAME_UNKNOWN                        = 0x0203;
}
