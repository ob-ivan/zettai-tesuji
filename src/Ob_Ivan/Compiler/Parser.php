<?php
namespace Ob_Ivan\Compiler;

class Parser
{
    // var //
    
    private $grammar;
    private $tokens;
    
    // public //
    
    public function __construct(Grammar $grammar, array $tokens)
    {
        $this->grammar  = $grammar;
        $this->tokens   = $tokens;
    }
    
    public function parse($ruleName, $position = 0)
    {
        return $this->grammar->getRule($ruleName)->parse($this->tokens, $position, $ruleName);
    }
}
