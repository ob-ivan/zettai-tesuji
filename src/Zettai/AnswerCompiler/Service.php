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
 *  PARENTHESIS_CLOSE       = ")" ;
 *  NON_SPECIAL_CHARACTER   = /[^(*)]/ ;
 *  CommentCharacter        = PARENTHESIS_OPEN / ASTERISK / NON_SPECIAL_CHARACTER ;
 *  AnyCharacter            = PARENTHESIS_OPEN / ASTERISK / PARENTHESIS_CLOSE / NON_SPECIAL_CHARACTER ;
 *  CommentText             = CommentCharacter* ;
 *  Comment                 = PARENTHESIS_OPEN ASTERISK CommentText PARENTHESIS_CLOSE ;
 *  Block                   = Comment / AnyCharacter ;
 *  Text                    = Block* ; // start symbol
**/
namespace Zettai\AnswerCompiler;

use Ob_Ivan\Compiler\Grammar;
use Ob_Ivan\Compiler\Lexer;
use Ob_Ivan\Compiler\TokenCollection;
use Ob_Ivan\Compiler\TokenStream;

class Service
{
    // var //
    
    private $lexingRules;
    private $grammar;
    
    // public //
    
    public function __construct()
    {
        $this->lexingRules = [
            '\\('       => TokenType::PARENTHESIS_OPEN,
            '\\*'       => TokenType::ASTERISK,
            '\\)'       => TokenType::PARENTHESIS_CLOSE,
            '[^(*)]+'   => TokenType::NON_SPECIAL_CHARACTER,
        ];
        
        $grammar = new Grammar(new NodeFactory);
        $grammar->setRule('PARENTHESIS_OPEN',       $grammar->terminal(TokenType::PARENTHESIS_OPEN));
        $grammar->setRule('ASTERISK',               $grammar->terminal(TokenType::ASTERISK));
        $grammar->setRule('PARENTHESIS_CLOSE',      $grammar->terminal(TokenType::PARENTHESIS_CLOSE));
        $grammar->setRule('NON_SPECIAL_CHARACTER',  $grammar->terminal(TokenType::NON_SPECIAL_CHARACTER));
        $grammar->setRule('CommentCharacter',       $grammar->orderedChoice('PARENTHESIS_OPEN', 'ASTERISK', 'NON_SPECIAL_CHARACTER'));
        $grammar->setRule('AnyCharacter',           $grammar->orderedChoice(
            'PARENTHESIS_OPEN', 'ASTERISK', 'PARENTHESIS_CLOSE', 'NON_SPECIAL_CHARACTER'
        ));
        $grammar->setRule('CommentText',            $grammar->zeroOrMore('CommentCharacter'));
        $grammar->setRule('Comment',                $grammar->sequence('PARENTHESIS_OPEN', 'ASTERISK', 'CommentText', 'PARENTHESIS_CLOSE'));
        $grammar->setRule('Block',                  $grammar->orderedChoice('Comment', 'AnyCharacter'));
        $grammar->setRule('Text',                   $grammar->zeroOrMore('Block'));
        $this->grammar = $grammar;
    }
    
    public function compile($source)
    {
        return $this->parse($this->tokenize($source))->build();
    }
    
    // private : compile steps //
    
    private function parse(TokenCollection $tokens)
    {
        return $this->grammar->parse(new TokenStream($tokens), 'Text');
    }
    
    /**
     * Разбивает входной текст на лексические элементы -- токены.
     *
     *  @param  string          $source
     *  @return TokenCollection
    **/
    private function tokenize($source)
    {
        // TODO: Завести LexerFactory(LexingRuleSet), чтобы лексер
        // не копировал набор правил, а брал из фабркии по необходимости.
        return (new Lexer($source, $this->lexingRules))->tokenize();
    }
}
