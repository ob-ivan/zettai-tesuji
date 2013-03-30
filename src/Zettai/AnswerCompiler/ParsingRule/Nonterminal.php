<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\ParsingRule;
use Zettai\AnswerCompiler\ParsingRuleSet;

class Nonterminal extends ParsingRule
{
    private $ruleName;
    
    /**
     *  @var <ParsingRuleSet>
    **/
    private $ruleSet;
    
    public function __construct(ParsingRuleSet $ruleSet, $ruleName)
    {
        $this->ruleSet  = $ruleSet;
        $this->ruleName = $ruleName;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $subNode = $this->ruleSet[$this->ruleName]->parse($tokens, $position, $this->ruleName);
        if (! $subNode) {
            return null;
        }
        return $this->produceNode($nodeClass, $subNode, $position, $subNode->length);
    }
}
