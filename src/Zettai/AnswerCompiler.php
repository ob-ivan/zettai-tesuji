<?php
/**
 * Преобразовывает сырой текст ответа в соответствии с разметкой в готовый html-код.
 *
 * Правила разметки:
 *  1.  Примечание. "(* текст )" превращается в "[*]", при наводе на который
 *      появляется всплывашка с текстом "текст".
**/
namespace Zettai;

use Zettai\AnswerCompiler\Token;

class AnswerCompiler
{
    // public //
    
    public function compile($source)
    {
        return $this->build($this->tokenize($source));
    }
    
    // private //
    
    private function build(array $tokens)
    {
        // TODO
    }
    
    private function tokenize($source)
    {
        $tokens = [];
        while (strlen($source) > 0) {
            if (preg_match('/\s*\(\*([^)]*)\)/', $source, $matches)) {
                $tokens[] = new Token(Token::TYPE_ANNOTATION, $matches[1]);
                $source = substr($source, strlen($matches[0]));
                continue;
            }
        }
    }
}
