<?php
namespace Zettai\AnswerCompiler\Node;

use Zettai\AnswerCompiler\Node;

class Text extends Node
{
    public function build()
    {
        $output = [];
        foreach ($this->children as $child) {
            $output[] = $child->build();
        }
        return implode('', $output);
    }
}
