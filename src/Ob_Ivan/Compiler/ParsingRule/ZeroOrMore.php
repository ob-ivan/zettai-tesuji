<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\TokenStream;

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
    
    public function parseExisting(TokenStream $stream, $nodeType = null)
    {
        $children = [];
        for ($subStream = $stream; ! $subStream->isEndOfStream(); $subStream = $subStream->offset($subNode->length)) {
            $subNode = $this->grammar->parse($subStream, $this->ruleName);
            if (! $subNode) {
                break;
            }
            $children[] = $subNode;
        }
        return $this->produceNode($nodeType, $stream->getPosition(), $subStream->diff($stream), $children);
    }
}
