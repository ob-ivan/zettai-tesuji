<?php
namespace Zettai\AnswerCompiler\Node;

use Ob_Ivan\Compiler\Node;

class Comment extends Node
{
    public function build()
    {
        $text = [];
        foreach ($this->children as $child) {
            if ($child instanceof CommentCharacter) {
                $text[] = $child->build();
            }
        }
        return '<a href="javascript:void(0)" title="' . htmlspecialchars(implode('', $text)) . '"><sup>[*]</sup></a>';
    }
}
