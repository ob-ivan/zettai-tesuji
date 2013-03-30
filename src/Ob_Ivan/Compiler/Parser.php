<?php
namespace Zettai\AnswerCompiler;

class Parser
{
    // var //
    
    private $tokens;
    private $ruleSet;
    
    // public //
    
    public function __construct(array $tokens, ParsingRuleSet $ruleSet)
    {
        $this->tokens  = $tokens;
        $this->ruleSet = $ruleSet;
    }
    
    public function parse($ruleName, $position = 0)
    {
        return $this->ruleSet[$ruleName]->parse($this->tokens, $position, $ruleName);
    }
}
