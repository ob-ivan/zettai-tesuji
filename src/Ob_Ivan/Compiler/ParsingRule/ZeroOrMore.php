<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;

class ZeroOrMore extends ParsingRule
{
    /**
     *  @var <ParsingRule>
    **/
    private $ruleName;
    
    public function __construct(Grammar $grammar, $ruleName)
    {
        parent::__construct($grammar);
        $this->ruleName = $ruleName;
    }
    
    public function parseExisting(array $tokens, $position, $nodeType = null)
    {
        $children = [];
        for ($offset = 0; isset($tokens[$position + $offset]); $offset += $subNode->length) {
            $subNode = $this->grammar->getRule($this->ruleName)->parse($tokens, $position + $offset, $this->ruleName);
            if (! $subNode) {
                break;
            }
            $children[] = $subNode;
        }
        return $this->produceNode($nodeType, $position, $offset, $children);
    }
}
