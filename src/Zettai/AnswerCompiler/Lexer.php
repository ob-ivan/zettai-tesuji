<?php
namespace Zettai\AnswerCompiler;

class Lexer
{
    // var //
    
    private $input;
    private $length;
    
    /**
     * Позиция в input'е, измеренная в мультибайтовых оффсетах.
    **/
    private $position;
    
    /**
     * Правила распознавания лексем.
     *
     *  @var [<string regexp> => <integer type>]
    **/
    private $rules;
    
    // public //
    
    public function __construct($input, array $rules)
    {
        $this->input = $input;
        $this->rules = $rules;
        
        $this->length = mb_strlen($input);
        $this->position = 0;
    }
    
    /**
     * Передвигает текущую позицию дальше, если передан
     * токен, находящийся на текущей позиции.
     *
     *  @param  Token       $token
     *  @throws Exception   LEXER_CONSUME_TOKEN_POSITION_WRONG
    **/
    public function consume(Token $token)
    {
        if ($token->position !== $this->position) {
            throw new Exception(
                'Token "' . get_class($token) . '" is attached to position ' . $token->position . '; ' .
                'unable to consume at position ' . $this->position,
                Exception::LEXER_CONSUME_TOKEN_POSITION_WRONG
            );
        }
        $this->position += $token->length;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Возвращает токен, находящийся на текущей позиции во входе,
     * или null, если вход не может быть прочтён.
     *
     * Внутренний счётчик при этом не передвигается.
     *
     *  @return Token
    **/
    public function getToken()
    {
        if ($this->isEndOfInput()) {
            return null;
        }
        
        foreach ($this->rules as $regexp => $type) {
            if (preg_match('/^' . $regexp . '/u', $this->getUnreadInput(), $matches)) {
                return new Token($type, $matches[0], $this->position, mb_strlen($matches[0]));
            }
        }
        return null;
    }
    
    public function getUnreadInput()
    {
        if ($this->position < $this->length) {
            return mb_substr($this->input, $this->position);
        }
        return '';
    }
    
    public function isEndOfInput()
    {
        return $this->position >= $this->length;
    }
    
    public function tokenize()
    {
        $tokens = [];
        
        while (! $this->isEndOfInput()) {
            $token = $this->getToken();
            if (! $token) {
                break;
            }
            $tokens[] = $token;
            $this->consume($token);
        }
        if (! $this->isEndOfInput()) {
            throw new Exception(
                'Unexpected characters at position ' . $this->getPosition() . ' in input: ' .
                '"' . $this->ellipsis($this->getUnreadInput()) . '"',
                Exception::LEXER_TOKENIZE_SOURCE_UNEXPECTED_CHARACTERS
            );
        }
        
        return $tokens;
    }
    
    // private //
    
    private function ellipsis($text, $length = 30)
    {
        if (mb_strlen($text) < $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }
    
    private function getChar()
    {
        return mb_substr($this->input, $this->position, 1);
    }
}
