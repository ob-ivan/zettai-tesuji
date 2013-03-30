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
        // TODO: Завести ParserFactory(ParsingRuleSet), чтобы не конструировать массив правил каждый раз.
        (new Parser($tokens, [
            'CommentCharacter' => new ParsingRule\OrderedChoice([
                new ParsingRule\Token(TokenType::PARENTHESIS_OPEN),
                new ParsingRule\Token(TokenType::ASTERISK),
                new ParsingRule\Token(TokenType::NON_SPECIAL_CHARACTER),
            ]),
            'AnyCharacter' => new ParsingRule\OrderedChoice([
                new ParsingRule\Token(TokenType::PARENTHESIS_OPEN),
                new ParsingRule\Token(TokenType::ASTERISK),
                new ParsingRule\Token(TokenType::PARENTHESIS_CLOSE),
                new ParsingRule\Token(TokenType::NON_SPECIAL_CHARACTER),
            ]),
            'CommentText' => new ParsingRule\ZeroOrMore(
                new ParsingRule\Nonterminal('CommentCharacter')
            ),
            'Comment' => new ParsingRule\Sequence([
                new ParsingRule\Token(TokenType::PARENTHESIS_OPEN),
                new ParsingRule\Token(TokenType::ASTERISK),
                new ParsingRule\Nonterminal('CommentText'),
                new ParsingRule\Token(TokenType::PARENTHESIS_CLOSE),
            ]),
            'Block' => new ParsingRule\OrderedChoice([
                new ParsingRule\Nonterminal('Comment'),
                new ParsingRule\Nonterminal('AnyCharacter'),
            ]),
            'Text' => new ParsingRule\ZeroOrMore(
                new ParsingRule\Nonterminal('Block')
            ),
        ]))->parse('Text');
    }
    
    /**
     * Разбивает входной текст на лексические элементы -- токены.
     *
     *  @param  string  $source
     *  @return [Token]
    **/
    private function tokenize($source)
    {
        // TODO: Завести LexerFactory(LexingRuleSet), чтобы не конструировать массив правил каждый раз.
        return (new Lexer($source, [
            '\\('       => TokenType::PARENTHESIS_OPEN,
            '\\*'       => TokenType::ASTERISK,
            '\\)'       => TokenType::PARENTHESIS_CLOSE,
            '[^(*)]'    => TokenType::NON_SPECIAL_CHARACTER,
        ]))->tokenize();
    }
}
