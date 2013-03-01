<?php
/**
 * Преобразовывает сырой текст ответа в соответствии с разметкой в готовый html-код.
 *
 * Правила разметки:
 *  1.  Примечание. "(* текст )" превращается в "[*]", при наводе на который
 *      появляется всплывашка с текстом "текст".
**/
namespace Zettai;

class AnswerCompiler
{
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
        // TODO
    }
}
