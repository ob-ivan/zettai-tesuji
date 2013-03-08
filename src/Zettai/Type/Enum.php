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
        parent::__construct($service);
        
        $this->views   = $views;
        $this->values  = $values;
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
     * TODO: Кэшировать отображение.
    **/
    public function fromView($view, $presentation)
    {
        $viewIndex = $this->getViewIndex($view);
        if (false === $viewIndex) {
            return null;
        }
        foreach ($this->values as $primitive => $views) {
            if ($views[$viewIndex] === $presentation) {
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
    
    public function toView($view, $primitive)
    {
        $viewIndex = $this->getViewIndex($view);
        if (false === $viewIndex) {
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
