<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\ParsingRule;

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
        return $this->produceNode($nodeClass, $children, $position, $offset);
    }
}
