<?php
namespace Zettai\AnswerCompiler\Node;

use Zettai\AnswerCompiler\Node;

class Group extends Node
{
    // public //
    
    public function build()
    {
        $builds = [];
        foreach ($this->children as $child) {
            $builds[] = $child->build();
        }
        return implode('', $builds);
    }
}
