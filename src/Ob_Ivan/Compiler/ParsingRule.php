<?php
namespace Ob_Ivan\Compiler;

use Zettai\AnswerCompiler\Node;
use Zettai\AnswerCompiler\NodeCollection;
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
    
    protected function produceNode($nodeClass, Token $token = null, array $children = null, $position, $length)
    {
        $collection = new NodeCollection;
        if ($children) {
            foreach ($children as $child) {
                $collection->append($child);
            }
        }
        return Node::produce($nodeClass, $token, $collection, $position, $length);
    }
}
