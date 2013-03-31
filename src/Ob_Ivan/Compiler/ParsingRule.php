<?php
namespace Ob_Ivan\Compiler;

abstract class ParsingRule implements ParsingRuleInterface
{
    protected $grammar;
    
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }
    
    public function parse(TokenStream $stream, $nodeClass = null)
    {
        if ($stream->isEndOfStream()) {
            return null;
        }
        return $this->parseExisting($stream, $nodeClass);
    }
    
    // protected //
    
    abstract protected function parseExisting(TokenStream $stream, $nodeClass = null);
    
    protected function produceNode($nodeClass, $position, $length, array $children = null, $value = null)
    {
        $collection = new NodeCollection;
        if ($children) {
            foreach ($children as $child) {
                $collection->append($child);
            }
        }
        $collection->freeze();
        return $this->grammar->produceNode($nodeClass, $position, $length, $collection, $value);
    }
}
