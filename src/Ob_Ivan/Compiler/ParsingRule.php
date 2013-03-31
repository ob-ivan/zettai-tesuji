<?php
namespace Ob_Ivan\Compiler;

abstract class ParsingRule implements ParsingRuleInterface
{
    protected $grammar;
    
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }
    
    public function parse(array $tokens, $position, $nodeClass = null)
    {
        if (! isset($tokens[$position])) {
            return null;
        }
        return $this->parseExisting($tokens, $position, $nodeClass);
    }
    
    // protected //
    
    abstract protected function parseExisting(array $tokens, $position, $nodeClass = null);
    
    protected function produceNode($nodeClass, $position, $length, array $children = null, $value = null)
    {
        $collection = new NodeCollection;
        if ($children) {
            foreach ($children as $child) {
                $collection->append($child);
            }
        }
        return $this->grammar->produceNode($nodeClass, $position, $length, $collection, $value);
    }
}
