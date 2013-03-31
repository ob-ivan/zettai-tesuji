<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\TokenStream;

class ZeroOrMore extends ParsingRule
{
    /**
     *  @var <string ruleName | ParsingRule rule>
    **/
    private $rule;
    
    public function __construct(Grammar $grammar, $rule)
    {
        parent::__construct($grammar);
        $this->rule = $rule;
    }
    
    public function parseExisting(TokenStream $stream, $nodeType = null)
    {
        $children = [];
        for ($subStream = $stream;
            ! $subStream->isEndOfStream();
            $subStream = $subStream->offset($subNode->length)
        ) {
            $subNode = $this->grammar->parse($subStream, $this->rule);
            if (! $subNode) {
                break;
            }
            $children[] = $subNode;
        }
        return $this->produceNode($nodeType, $stream->getPosition(), $subStream->diff($stream), $children);
    }
}
