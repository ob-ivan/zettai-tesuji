<?php
namespace Ob_Ivan\Compiler;

class ParserFactory
{
    private $ruleSet;
    private $nodeFactory;
    
    public function __construct(ParsingRuleSet $ruleSet, NodeFactoryInterface $nodeFactory)
    {
        $this->ruleSet      = $ruleSet;
        $this->nodeFactory  = $nodeFactory;
    }
    
    public function produce(array $tokens)
    {
        return new Parser($tokens, $this->ruleSet, $this->nodeFactory);
    }
}
