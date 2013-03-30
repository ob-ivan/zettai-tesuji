<?php
namespace Zettai\AnswerCompiler;

class Lexer
{
    // var //
    
    private $input;
    private $length;
    
    // Позиция в input'е, измеренная в мультибайтовых оффсетах.
    private $position;
    
    // public //
    
    public function __construct($input)
    {
        $this->input = $input;
        
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
     * TODO: Сделать перебор конфигурируемым.
     *
     *  @return Token
    **/
    public function getToken()
    {
        if ($this->isEndOfInput()) {
            return null;
        }
        
        $char = $this->getChar();
        $type = null;
        switch ($char) {
            case '(': $type = Token::T_PARENTHESIS_OPEN;    break;
            case '*': $type = Token::T_ASTERISK;            break;
            case ')': $type = Token::T_PARENTHESIS_CLOSE;   break;
            default : $type = Token::T_CHARACTER;           break;
        }
        return new Token($type, $char, $this->position, 1);
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
    
    // private //
    
    private function getChar()
    {
        return mb_substr($this->input, $this->position, 1);
    }
}
