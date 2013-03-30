<?php
namespace Zettai\AnswerCompiler;

use Zettai\Exception as VendorException;

class Exception extends VendorException
{
    const LEXER_CONSUME_TOKEN_POSITION_WRONG            = 0x0201;
    const LEXER_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS   = 0x0202;
    const NODE_COLLECTION_IS_FROZEN                     = 0x0203;
    const NODE_COLLECTION_OFFSET_SET_VALUE_TYPE_WRONG   = 0x0204;
    const NODE_GET_NAME_UNKNOWN                         = 0x0205;
    const PARSING_RULE_SET_OFFSET_SET_VALUE_TYPE_WRONG  = 0x0206;
    const TOKEN_GET_NAME_UNKNOWN                        = 0x0207;
}
