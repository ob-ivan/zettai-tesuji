<?php
namespace Zettai\AnswerCompiler;

interface ParsingRuleInterface
{
    public function parse(array $tokens, $position, $nodeClass = null);
}
