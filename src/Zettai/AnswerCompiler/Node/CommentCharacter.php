<?php
namespace Zettai\AnswerCompiler\Node;

use Zettai\AnswerCompiler\Node;

class CommentCharacter extends Node
{
    public function build()
    {
        print '<pre>' . __METHOD__ . ': this->content = ' . print_r($this->content, true) . '</pre>'; // debug
        die;
    }
}
