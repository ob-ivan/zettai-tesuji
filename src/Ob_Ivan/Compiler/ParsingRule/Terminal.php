<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\Node;
use Ob_Ivan\Compiler\ParsingRule;

class Terminal extends ParsingRule
{
    private $type;
    
    public function __construct(Grammar $grammar, $type)
    {
        parent::__construct($grammar);
        $this->type = $type;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $token = $tokens[$position];
        if ($token->type === $this->type) {
            return new Node\Terminal($position, 1, null, $token->value);
        }
        return null;
    }
}
