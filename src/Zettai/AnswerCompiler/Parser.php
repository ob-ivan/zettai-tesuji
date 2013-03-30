<?php
namespace Zettai\AnswerCompiler;

class Parser
{
    // var //
    
    private $tokens;
    private $ruleSet;
    
    // public //
    
    public function __construct(array $tokens, $rules)
    {
        $this->tokens  = $tokens;
        $this->ruleSet = new ParsingRuleSet($rules);
    }
    
    public function parse($ruleName, $position = 0)
    {
        return $this->ruleSet[$ruleName]->parse($this->tokens, $position, $ruleName);
    }
}
