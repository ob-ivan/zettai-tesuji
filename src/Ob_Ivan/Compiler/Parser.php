<?php
namespace Ob_Ivan\Compiler;

class Parser
{
    // var //
    
    private $grammar;
    private $tokens;
    
    // public //
    
    public function __construct(Grammar $grammar, TokenCollection $tokens)
    {
        $this->grammar  = $grammar;
        $this->tokens   = $tokens;
    }
    
    public function parse($ruleName, $position = 0)
    {
        return $this->grammar->getRule($ruleName)->parse(new TokenStream($this->tokens, $position), $ruleName);
    }
}
