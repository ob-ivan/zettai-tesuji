<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\ParsingRule;

class Sequence extends ParsingRule
{
    private $ruleName;
    
    /**
     *  @var [<ParsingRule>]
    **/
    private $components;
    
    public function __construct(array $components)
    {
        $this->components = $components;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        $offset = 0;
        $children = [];
        foreach ($this->components as $subRule) {
            $subNode = $subRule->parse($tokens, $position + $offset);
            if (! $subNode) {
                return null;
            }
            $children[] = $subNode;
            $offset += $subNode->length;
        }
        return $this->produceNode($nodeClass, $children, $position, $offset);
    }
}
