<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\ParsingRule;

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
                return $this->produceNode($nodeClass, null, [$subNode], $position, $subNode->length);
            }
        }
        return null;
    }
}
