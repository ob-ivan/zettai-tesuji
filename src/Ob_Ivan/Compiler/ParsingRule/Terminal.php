<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\ParsingRule;

class Terminal extends ParsingRule
{
    private $type;
    
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $token = $tokens[$position];
        if ($token->type === $this->type) {
            return $this->produceNode($nodeClass, $token, [], $position, 1);
        }
        return null;
    }
}
