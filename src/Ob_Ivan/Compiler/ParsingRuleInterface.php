<?php
namespace Ob_Ivan\Compiler;

interface ParsingRuleInterface
{
    public function parse(array $tokens, $position, $nodeClass = null);
}
