<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\TokenStream;

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
    
    public function parseExisting(TokenStream $stream, $nodeType = null)
    {
        foreach ($this->variants as $ruleName) {
            $subNode = $this->grammar->parse($stream, $ruleName);
            if ($subNode) {
                return $this->produceNode($nodeType, $stream->getPosition(), $subNode->length, [$subNode]);
            }
        }
        return null;
    }
}
