<?php
namespace Ob_Ivan\Compiler;

class Parser
{
    // var //
    
    private $tokens;
    private $ruleSet;
    private $nodeFactory;
    
    // public //
    
    public function __construct(array $tokens, ParsingRuleSet $ruleSet, NodeFactoryInterface $nodeFactory)
    {
        $this->tokens       = $tokens;
        $this->ruleSet      = $ruleSet;
        $this->nodeFactory  = $nodeFactory;
    }
    
    public function parse($ruleName, $position = 0)
    {
        return $this->ruleSet[$ruleName]->parse($this->tokens, $position, $ruleName);
    }
}
