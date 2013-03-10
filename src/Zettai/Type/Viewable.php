<?php
namespace Zettai\Type;

class Viewable extends Type
{
    // var //
    
    /**
     *  @param  [<viewIndex> => <viewName>]     $views
    **/
    private $views  = [];
    
    /**
     *  @param  [<primitive> => [<viewIndex> => <presentation>]]    $values
    **/
    private $values = [];
    
    // public //
    
    public function __construct(ServiceInterface $service, array $values)
    {
        parent::__construct($service);
        
        $this->views   = $service['view']->each();
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
            if (0 === strpos($presentation, $views[$viewIndex])) {
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
        return $this->value($primitive);
    }
    
    public function toView($view, $primitive)
    {
        $viewIndex = $this->getViewIndex($view);
        if (false === $viewIndex) {
            throw new Exception(
                'View "' . $view . '" is not supported by this type',
                Exception::VIEWABLE_TO_VIEW_UNSUPPORTED_VIEW
            );
        }
        if (! isset($this->values[$primitive][$viewIndex])) {
            throw new Exception(
                'View "' . $view . '" is not supported for value "' . $primitive . '"',
                Exception::VIEWABLE_TO_VIEW_UNSUPPORTED_PRIMITIVE
            );
        }
        return $this->values[$primitive][$viewIndex];
    }
    
    // private //
    
    private function getViewIndex($view)
    {
        // TODO: Кэшировать отдачу.
        return array_search($view->toView('whatever'), $this->views);
    }
}
