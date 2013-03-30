<?php
/**
 * Преобразовывает сырой текст ответа в соответствии с разметкой в готовый html-код.
 *
 * Правила разметки:
 *  1.  Примечание. "(* комментарий )" превращается в "[*]", при наводе на который
 *      появляется всплывашка с текстом "комментарий".
 *
 * Формальная грамматика:
 *  PARENTHESIS_OPEN        = "(" ;
 *  ASTERISK                = "*" ;
 *  PARENTTHESIS_CLOSE      = ")" ;
 *  NON_SPECIAL_CHARACTER   = /[^(*)]/ ;
 *  CommentCharacter        = PARENTHESIS_OPEN / ASTERISK / NON_SPECIAL_CHARACTER ;
 *  AnyCharacter            = PARENTHESIS_OPEN / ASTERISK / PARENTTHESIS_CLOSE / NON_SPECIAL_CHARACTER ;
 *  CommentText             = CommentCharacter* ;
 *  Comment                 = PARENTHESIS_OPEN ASTERISK CommentText PARENTTHESIS_CLOSE ;
 *  Block                   = Comment / AnyCharacter ;
 *  Text                    = Block* ;
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
     *  @param  string  $source
     *  @return [Token]
    **/
    private function tokenize($source)
    {
        return (new Lexer($source, [
            '\\('       => TokenType::PARENTHESIS_OPEN,
            '\\*'       => TokenType::ASTERISK,
            '\\)'       => TokenType::PARENTHESIS_CLOSE,
            '[^(*)]'    => TokenType::NON_SPECIAL_CHARACTER,
        ]))->tokenize();
    }
}
