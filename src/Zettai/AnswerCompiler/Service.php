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
    // var //
    
    private $lexingRules;
    private $parsingRules;
    
    // public //
    
    public function __construct()
    {
        $this->lexingRules = [
            '\\('       => TokenType::PARENTHESIS_OPEN,
            '\\*'       => TokenType::ASTERISK,
            '\\)'       => TokenType::PARENTHESIS_CLOSE,
            '[^(*)]'    => TokenType::NON_SPECIAL_CHARACTER,
        ];
        
        $ruleSet = new ParsingRuleSet;
        $ruleSet->addRules([
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
                new ParsingRule\Nonterminal($ruleSet, 'CommentCharacter')
            ),
            'Comment' => new ParsingRule\Sequence([
                new ParsingRule\Token(TokenType::PARENTHESIS_OPEN),
                new ParsingRule\Token(TokenType::ASTERISK),
                new ParsingRule\Nonterminal($ruleSet, 'CommentText'),
                new ParsingRule\Token(TokenType::PARENTHESIS_CLOSE),
            ]),
            'Block' => new ParsingRule\OrderedChoice([
                new ParsingRule\Nonterminal($ruleSet, 'Comment'),
                new ParsingRule\Nonterminal($ruleSet, 'AnyCharacter'),
            ]),
            'Text' => new ParsingRule\ZeroOrMore(
                new ParsingRule\Nonterminal($ruleSet, 'Block')
            ),
        ]);
        $this->parsingRuleSet = $ruleSet;
    }
    
    public function compile($source)
    {
        return $this->parse($this->tokenize($source))->build();
    }
    
    // private : compile steps //
    
    private function parse(array $tokens)
    {
        return (new Parser($tokens, $this->parsingRuleSet))->parse('Text');
    }
    
    /**
     * Разбивает входной текст на лексические элементы -- токены.
     *
     *  @param  string  $source
     *  @return [Token]
    **/
    private function tokenize($source)
    {
        // TODO: Завести LexerFactory(LexingRuleSet), чтобы лексер
        // не копировал набор правил, а брал из фабркии по необходимости.
        return (new Lexer($source, $this->lexingRules))->tokenize();
    }
}
