<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\ParsingRule;

class ZeroOrMore extends ParsingRule
{
    /**
     *  @var <ParsingRule>
    **/
    private $subRule;
    
    public function __construct(ParsingRule $subRule)
    {
        $this->subRule = $subRule;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $children = [];
        for ($offset = 0; isset($tokens[$position + $offset]); $offset += $subNode->length) {
            $subNode = $this->subRule->parse($tokens, $position + $offset);
            if (! $subNode) {
                break;
            }
            $children[] = $subNode;
        }
        return $this->produceNode($nodeClass, null, $children, $position, $offset);
    }
}
