<?php
namespace Zettai\AnswerCompiler\Node;

use Ob_Ivan\Compiler\Node;

class Comment extends Node
{
    public function build()
    {
        foreach ($this->children as $child) {
            if ($child instanceof CommentText) {
                $text = $child->build();
                return '<a href="javascript:void(0)" title="' . htmlspecialchars($text) . '"><sup>[*]</sup></a>';
            }
        }
    }
}