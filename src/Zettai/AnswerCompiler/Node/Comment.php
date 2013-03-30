<?php
namespace Zettai\AnswerCompiler\Node;

use Zettai\AnswerCompiler\Node;

class Comment extends Node
{
    public function build()
    {
        foreach ($this->children as $child) {
            if ($child instanceof CommentText) {
                $text = $child->build();
                return '<a href="javascript:void(0)" title="' . htmlcharacters($text) . '"><sup>[*]</sup></a>';
            }
        }
    }
}
