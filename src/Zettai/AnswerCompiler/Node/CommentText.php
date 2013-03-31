<?php
namespace Zettai\AnswerCompiler\Node;

use Ob_Ivan\Compiler\Node;

class CommentText extends Node
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
