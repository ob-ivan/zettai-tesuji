<?php
namespace Ob_Ivan\Compiler;

use Exception as ParentException;

class Exception extends ParentException
{
    const LEXER_CONSUME_TOKEN_POSITION_WRONG            = 0x010101;
    const LEXER_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS   = 0x010102;
    
    const NODE_COLLECTION_OFFSET_SET_PROHIBITED         = 0x010201;
    const NODE_COLLECTION_OFFSET_UNSET_PROHIBITED       = 0x010202;
    
    const NODE_GET_NAME_UNKNOWN                         = 0x010301;
    
    const PARSING_RULE_SET_OFFSET_SET_VALUE_TYPE_WRONG  = 0x010401;
    
    const TOKEN_COLLECTION_GET_NAME_UNKNOWN             = 0x010501;
    const TOKEN_COLLECTION_OFFSET_SET_PROHIBITED        = 0x010502;
    const TOKEN_COLLECTION_OFFSET_UNSET_PROHIBITED      = 0x010503;
    
    const TOKEN_GET_NAME_UNKNOWN                        = 0x010601;
    
}
