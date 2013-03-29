<?php
namespace Zettai\AnswerCompiler;

abstract class Node
{
    // var //
    
    protected $children = [];
    
    // public //
    
    public function append(self $child)
    {
        $this->children[] = $child;
    }
    
    abstract public function build();
}
