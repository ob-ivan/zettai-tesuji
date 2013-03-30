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
    
    // private : compile steps //
    
    private function parse(array $tokens)
    {
        $node = new Node\Group;
        foreach ($tokens as $token) {
            $node->append(new Node\Text($token->value));
        }
        return $node;
    }
    
    /**
     * Разбивает входной текст на лексические элементы -- токены.
     *
     * TODO: Абстрагировать заложенные механизмы и описывать как набор правил.
    **/
    private function tokenize($source)
    {
        $tokens = [];
        
        while (! empty($source)) {
            $length = strlen($source);
            
            // TODO
            
            if ($length === strlen($source)) {
                break;
            }
        }
        if (! empty($source)) {
            throw new Exception(
                'Unexpected characters at end of source: "' . $this->ellipsis($source) . '"',
                Exception::SERVICE_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS
            );
        }
        
        return $tokens;
    }
    
    // private : helpers //
    
    private function ellipsis($text, $length = 30)
    {
        if (mb_strlen($text) < $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }
}
