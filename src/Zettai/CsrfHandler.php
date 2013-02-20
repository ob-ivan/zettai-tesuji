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
    
    const HASH_SALT  = 'KSt;tE:0c-j9N5_6';
    const KEY_PREFIX = 'csrf_';
    
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
        $key  = $this->key($csrf);
        $this->session->set($key, $seed);
        return $csrf;
    }
    
    /**
     * Проверяет, соответствует ли переданный формой хэш
     * ранее сохранённому значению.
    **/
    public function validate($csrf, $test)
    {
        $key  = $this->key($csrf);
        if (! $this->session->has($key)) {
            return false;
        }
        $seed = $this->session->get($key);
        return $seed === $test;
    }
    
    // private //
    
    private function hash($seed)
    {
        return md5($seed . microtime(true) . self::HASH_SALT);
    }
    
    private function key($csrf)
    {
        return self::KEY_PREFIX . $csrf;
    }
}
