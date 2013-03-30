<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\Node;

class Token
{
    private $type;
    
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    public function parse(array $tokens, $position, $nodeClass = null)
    {
        if (! isset($tokens[$position])) {
            return null;
        }
        $token = $tokens[$position];
        if ($token instanceof Token && $token->type === $this->type) {
            return Node::produce($nodeClass, $token, $position, 1);
        }
        return null;
    }
}
