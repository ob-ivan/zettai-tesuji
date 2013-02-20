<?php
/**
 * Обработчик и валидатор csrf-токенов.
 *
 * Порождает токены и сохраняет их в сессию.
 * Отвечает на вопрос, соответствует ли токен ранее сгенерированному значению.
 *
 * Как применять:
 *  - При выводе формы вставляем токен:
 *      <input type="hidden" name="csrf" value="{{ csrfHandler.generate('operation_name_' ~ operation_id) }}"/>
 *  - В обработчике формы сравниваем токен с ранее сохранённым:
 *      if (! $csrfHandler->validate ($request->request->get('csrf'), 'operation_name_' . $operation_id)) {
 *          throw new Exception('Попытка взлома');
 *      }
**/
namespace Zettai;

use Symfony\Component\HttpFoundation\Session\Session;

class CsrfHandler
{
    // const //
    
    const SALT = 'KSt;tE:0c-j9N5_6';
    
    // public //
    
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    /**
     * Порождает хэш для вывода в форму.
    **/
    public function generate($seed)
    {
        $csrf = $this->hash($seed);
        $this->session->set($csrf, $seed);
        return $csrf;
    }
    
    /**
     * Проверяет, соответствует ли переданный формой хэш
     * ранее сохранённому значению.
    **/
    public function validate($csrf, $test)
    {
        if (! $this->session->has($csrf)) {
            return false;
        }
        $seed = $this->session->get($csrf);
        return $seed === $test;
    }
    
    // private //
    
    private function hash($seed)
    {
        return md5($seed . microtime(true) . self::SALT);
    }
}
