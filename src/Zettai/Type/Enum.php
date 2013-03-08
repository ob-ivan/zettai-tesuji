<?php
namespace Zettai\Type;

class Enum extends Type
{
    // var //
    
    private $views  = [];
    private $values = [];
    
    // public //
    
    /**
     *  @param  [<viewIndex> => <viewValue>]                        $views
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
    **/
    public function __construct(Service $service, array $views, array $values)
    {
        $this->service = $service;
        $this->views   = $views;
        $this->values  = $values;
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
                    Exception::ENUM_FROM_VIEW_ARGUMENT_ABSENT
                );
            }
            $view = $service->getViewByName($matches[1]);
            if (! $view) {
                throw new Exception(
                    'Unknown view "' . $matches[1] . '"',
                    Exception::ENUM_FROM_VIEW_UNKNOWN_VIEW
                );
            }
            return $this->fromView($view, $args[0]);
        }
        throw new Exception('Method "' . $name . '" is unknown', Exception::ENUM_CALL_METHOD_UNKNOWN);
    }
    
    /**
     * Короткая запись для инстанциации значения.
    **/
    public function __get($name)
    {
        return $this->from($name);
    }
    
    /**
     * Возвращает список значений как объектов.
     *
     * TODO: Добавить обработку коллбэком.
     * TODO: Кэшировать отдачу.
     *
     *  @return [Value]
    **/
    public function each()
    {
        $return = [];
        foreach ($this->values as $primitive => $views) {
            $return[$primitive] = $this->fromPrimitive($primitive);
        }
        return $return;
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
        foreach ($this->service->getViews() as $view) {
            $candidate = $this->fromView($view, $input);
            if ($candidate) {
                return $candidate;
            }
        }
        return null;
    }
    
    /**
     * TODO: Кэшировать отображение.
    **/
    public function fromView($view, $input)
    {
        $viewIndex = $this->getViewIndex($view);
        if (! $viewIndex) {
            return null;
        }
        foreach ($this->values as $primitive => $views) {
            if ($views[$viewIndex] === $args[0]) {
                return $this->fromPrimitive($primitive);
            }
        }
        return null;
    }
    
    public function fromPrimitive($primitive)
    {
        if (! isset($this->values[$primitive])) {
            return null;
        }
        return new Value($this, $primitive);
    }
    
    public function toViewByName($viewName, $primitive)
    {
        $view = $service->getViewByName($viewName);
        if (! $view) {
            throw new Exception('Unknown view name "' . $viewName . '"', Exception::ENUM_TO_VIEW_NAME_UNKNOWN);
        }
        $viewIndex = $this->getViewIndex($view);
        if (! $viewIndex) {
            throw new Exception('View "' . $viewName . '" is not supported by this type', Exception::ENUM_TO_VIEW_UNSUPPORTED_VIEW);
        }
        if (! isset($this->values[$primitive][$viewIndex])) {
            throw new Exception('View "' . $viewName . '" is not supported for this value', Exception::ENUM_TO_VIEW_UNSUPPORTED_PRIMITIVE);
        }
        return $this->values[$primitive][$viewIndex];
    }
    
    // private //
    
    private function getViewIndex($view)
    {
        // TODO: Кэшировать отдачу.
        return array_search($view, $this->views);
    }
}
