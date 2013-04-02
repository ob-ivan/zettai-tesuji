<?php
namespace Ob_Ivan\EviType;

interface TypeSortInterface
{
    /**
     * Обрабатывает вызовы методов на типе.
     *
     *  @param  Type    $type
     *  @param  string  $method
     *  @param  array   $arguments
    **/
    public function call(Type $type, $method, array $arguments);
    
    /**
     * Конструирует тип по переданным аргументам.
     *
     *  @param  array   $arguments
     *  @return Type
    **/
    public function produce(array $arguments);
    
    /**
     * Конструирует стандартный для этого сорта сервис представлений.
     *
     *  @param  Type        $type
     *  @return ViewService
    **/
    public function view(Type $type);
}
