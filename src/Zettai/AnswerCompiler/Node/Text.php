<?php
namespace Zettai\AnswerCompiler\Node;

use Zettai\AnswerCompiler\Node;

class Text extends Node
{
    // var //
    
    private $text;
    
    // public //
    
    public function __construct($text)
    {
        $this->text = $text;
    }
    
    public function build()
    {
        return $this->text;
    }
}
