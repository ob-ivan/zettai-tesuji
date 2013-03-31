<?php
namespace Zettai\AnswerCompiler\Node;

use Ob_Ivan\Compiler\Node;

class AnyCharacter extends Node
{
    public function build()
    {
        return $this->children[0]->value;
    }
}
