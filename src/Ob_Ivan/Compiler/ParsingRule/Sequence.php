<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\TokenStream;

class Sequence extends ParsingRule
{
    /**
     *  @var [<ruleName>]
    **/
    private $components;
    
    public function __construct(Grammar $grammar, array $components)
    {
        parent::__construct($grammar);
        $this->components   = $components;
    }
    
    public function parseExisting(TokenStream $stream, $nodeType = null)
    {
        $offset = 0;
        $children = [];
        foreach ($this->components as $ruleName) {
            $subNode = $this->grammar->parse($stream->offset($offset), $ruleName);
            if (! $subNode) {
                return null;
            }
            $children[] = $subNode;
            $offset += $subNode->length;
        }
        return $this->produceNode($nodeType, $stream->getPosition(), $offset, $children);
    }
}
