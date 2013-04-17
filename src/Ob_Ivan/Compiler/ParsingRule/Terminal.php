<?php
namespace Ob_Ivan\Compiler\ParsingRule;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\Node;
use Ob_Ivan\Compiler\ParsingRule;
use Ob_Ivan\Compiler\TokenStream;

class Terminal extends ParsingRule
{
    private $type;

    public function __construct(Grammar $grammar, $type)
    {
        parent::__construct($grammar);
        $this->type = $type;
    }

    public function parse(TokenStream $stream, $nodeClass = null)
    {
        if ($stream->isEndOfStream()) {
            return null;
        }
        $token = $stream->getCurrentToken();
        if ($token->type === $this->type) {
            return new Node\Terminal($stream->getPosition(), 1, null, $token->value);
        }
        return null;
    }
}
