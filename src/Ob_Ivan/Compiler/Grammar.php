<?php
namespace Ob_Ivan\Compiler;

class Grammar
{
    // var //
    
    private $nodeFactory;
    
    /**
     * Правила для нетерминальных символов.
     *
     *  @var [<ruleName> => <ParsingRule rule>]
    **/
    private $ruleSet = [];
    
    // public //
    
    public function __construct(NodeFactoryInterface $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }
    
    public function produceParser(array $tokens)
    {
        return new Parser($this, $tokens);
    }
    
    public function produceNode($nodeClass, $position, $length, NodeCollection $collection, $value = null)
    {
        return $this->nodeFactory->produce($nodeClass, $position, $length, $collection, $value);
    }
    
    // public : rule manipulation //
    
    public function getRule($ruleName)
    {
        return $this->rules[$ruleName];
    }
    
    public function setRule($ruleName, ParsingRuleInterface $parsingRule)
    {
        $this->rules[$ruleName] = $parsingRule;
    }
    
    // public : rule production //
    
    public function orderedChoice()
    {
        return new ParsingRule\OrderedChoice($this, func_get_args());
    }
    
    public function sequence()
    {
        return new ParsingRule\Sequence($this, func_get_args());
    }
    
    public function terminal($tokenType)
    {
        return new ParsingRule\Terminal($this, $tokenType);
    }
    
    public function zeroOrMore($ruleName)
    {
        return new ParsingRule\ZeroOrMore($this, $ruleName);
    }
}
