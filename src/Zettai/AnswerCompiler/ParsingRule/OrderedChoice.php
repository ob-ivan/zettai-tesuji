<?php
namespace Zettai\AnswerCompiler\ParsingRule;

use Zettai\AnswerCompiler\ParsingRule;

class OrderedChoice extends ParsingRule
{
    /**
     *  @var [<ParsingRule>]
    **/
    private $variants;
    
    public function __construct(array $variants)
    {
        $this->variants = $variants;
    }
    
    public function parseExisting(array $tokens, $position, $nodeClass = null)
    {
        foreach ($this->variants as $subRule) {
            $subNode = $subRule->parse($tokens, $position);
            if ($subNode) {
                return $this->produceNode($nodeClass, $subNode, $position, $subNode->length);
            }
        }
        return null;
    }
}
