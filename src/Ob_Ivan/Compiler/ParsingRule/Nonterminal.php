<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\NodeCollection;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\ParsingRuleSet;

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
        return $this->produceNode($nodeClass, null, [$subNode], $position, $subNode->length);
    }
}
