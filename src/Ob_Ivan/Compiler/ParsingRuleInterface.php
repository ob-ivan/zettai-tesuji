<?php
namespace Ob_Ivan\Compiler;

interface ParsingRuleInterface
{
    public function parse(TokenStream $stream, $nodeType = null);
}
