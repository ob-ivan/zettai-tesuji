<?php
namespace Zettai\AnswerCompiler;

use Zettai\AnswerCompiler\Node;
use Zettai\AnswerCompiler\Token;

abstract class ParsingRule implements ParsingRuleInterface
{
    public function parse(array $tokens, $position, $nodeClass = null)
    {
        if (! isset($tokens[$position])) {
            return null;
        }
        return $this->parseExisting($tokens, $position, $nodeClass);
    }
    
    // protected //
    
    abstract protected function parseExisting(array $tokens, $position, $nodeClass = null);
    
    protected function produceNode($nodeClass, Token $token = null, array $children, $position, $length)
    {
        return Node::produce($nodeClass, $token, $children, $position, $length);
    }
}
