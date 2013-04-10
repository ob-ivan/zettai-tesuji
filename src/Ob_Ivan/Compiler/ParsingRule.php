<?php
namespace Ob_Ivan\Compiler;

abstract class ParsingRule implements ParsingRuleInterface
{
    protected $grammar;

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    // protected //

    protected function produceNode($nodeClass, $position, $length, array $children = null, $value = null)
    {
        return $this->grammar->produceNode($nodeClass, $position, $length, NodeCollection::fromArray($children), $value);
    }
}
