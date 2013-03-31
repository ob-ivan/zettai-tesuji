<?php
namespace Ob_Ivan\Compiler;

class TokenStream
{
    // var //
    
    private $collection;
    private $position;
    
    private $currentToken  = null;
    private $isEndOfStream = null;
    
    // public //
    
    public function __construct(TokenCollection $collection, $position = 0)
    {
        $this->collection    = $collection;
        $this->position      = $position;
    }
    
    public function diff(self $operand)
    {
        return $this->position - $operand->position;
    }
    
    public function getCurrentToken()
    {
        if (is_null($this->currentToken)) {
            $this->currentToken = $this->collection[$this->position];
        }
        return $this->currentToken;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function isEndOfStream()
    {
        if (is_null($this->isEndOfStream)) {
            $this->isEndOfStream = $this->position >= $this->collection->length;
        }
        return $this->isEndOfStream;
    }
    
    public function offset($offset)
    {
        return new self($this->collection, $this->position + $offset);
    }
}
