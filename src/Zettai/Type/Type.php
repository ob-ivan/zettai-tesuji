<?php
namespace Zettai\Type;

abstract class Type implements TypeInterface
{
    // var //
    
    private $service;
    
    // public //
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
    
    /**
     * Волшебные методы:
     *  - from<View>($presentation)
    **/
    public function __call($name, $args)
    {
        if (preg_match('/^from(\w+)$/i', $name, $matches)) {
            if (! isset($args[0])) {
                throw new Exception(
                    'Method "' . $name . '" expects at least one argument',
                    Exception::TYPE_FROM_VIEW_ARGUMENT_ABSENT
                );
            }
            $view = $this->service['view']->from($matches[1]);
            if (! $view) {
                throw new Exception(
                    'Unknown view "' . $matches[1] . '"',
                    Exception::TYPE_FROM_VIEW_UNKNOWN_VIEW
                );
            }
            return $this->fromView($view, $args[0]);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::TYPE_CALL_METHOD_UNKNOWN);
    }
    
    /**
     * Короткая запись для инстанциации значения.
    **/
    public function __get($name)
    {
        return $this->from($name);
    }
    
    public function equals($a, $b)
    {
        return $a === $b;
    }
    
    /**
     * Пытается распознать в аргументе какое-либо из значений.
     *
     * TODO: Кэшировать отображение.
     *
     *  @param  mixed   $input
     *  @return Value
    **/
    public function from($input)
    {
        // Проверить, вдруг, уже правильного типа.
        if ($this->has($input)) {
            return $input;
        }
        // Попробовать примитивное значение.
        $fromPrimitive = $this->fromPrimitive($input);
        if ($fromPrimitive) {
            return $fromPrimitive;
        }
        // Поискать среди представлений.
        foreach ($this->service['view']->each() as $view) {
            $candidate = $this->fromView($view, $input);
            if ($candidate) {
                return $candidate;
            }
        }
        return null;
    }
    
    abstract public function fromView($view, $presentation);
    
    public function has($value)
    {
        if (! $value instanceof ValueInterface) {
            return false;
        }
        return $value->is($this);
    }
    
    public function toString($internal)
    {
        foreach ($this->service['view']->each() as $view) {
            return $this->toView($view, $internal);
        }
    }
    
    abstract public function toView($view, $primitive);
    
    public function toViewByName($viewName, $primitive)
    {
        $view = $this->service['view']->from($viewName);
        if (! $view) {
            throw new Exception('Unknown view name "' . $viewName . '"', Exception::ENUM_TO_VIEW_NAME_UNKNOWN);
        }
        return $this->toView($view, $primitive);
    }
    
    public function value($internal)
    {
        return new Value($this, $internal);
    }
}
