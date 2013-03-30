<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\ParsingRule;

class Token extends ParsingRule
{
    private $type;
    
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $token = $tokens[$position];
        if ($token instanceof Token && $token->type === $this->type) {
            return $this->produceNode($nodeClass, $token, $position, 1);
        }
        return null;
    }
}
