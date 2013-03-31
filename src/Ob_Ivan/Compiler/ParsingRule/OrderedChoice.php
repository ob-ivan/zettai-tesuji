<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;

class OrderedChoice extends ParsingRule
{
    /**
     *  @var [<ruleName>]
    **/
    private $variants;
    
    public function __construct(Grammar $grammar, array $variants)
    {
        parent::__construct($grammar);
        $this->variants = $variants;
    }
    
    public function parseExisting(array $tokens, $position, $nodeType = null)
    {
        foreach ($this->variants as $ruleName) {
            $subNode = $this->grammar->getRule($ruleName)->parse($tokens, $position, $ruleName);
            if ($subNode) {
                return $this->produceNode($nodeType, $position, $subNode->length, [$subNode]);
            }
        }
        return null;
    }
}
