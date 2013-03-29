<?php
/**
 * Преобразовывает сырой текст ответа в соответствии с разметкой в готовый html-код.
 *
 * Правила разметки:
 *  1.  Примечание. "(* текст )" превращается в "[*]", при наводе на который
 *      появляется всплывашка с текстом "текст".
**/
namespace Zettai\AnswerCompiler;

class Service
{
    // public //
    
    public function compile($source)
    {
        return $this->parse($this->tokenize($source))->build();
    }
    
    // private //
    
    private function parse(array $tokens)
    {
        $node = new Node\Group;
        foreach ($tokens as $token) {
            $node->append(new Node\Text($token->value));
        }
        return $node;
    }
    
    private function tokenize($source)
    {
        $tokens = [];
        $tokens[] = new Token\Text($source);
        return $tokens;
    }
}
