<?php
namespace Zettai\Type\Type;

use Zettai\Type\Value;
use Zettai\Type\ValueInterface;
use Zettai\Type\View\Service as ViewService;

abstract class Type implements TypeInterface
{
    // var //
    
    /**
     *  @var ServiceInterface
    **/
    private $typeService;
    
    /**
     *  @var ViewService
    **/
    private $view;
    
    // public //
    
    public function __construct(ServiceInterface $typeService)
    {
        $this->typeService = $typeService;
        
        $this->view = new ViewService($this);
    }
    
    /**
     * Волшебные свойства:
     *  - view открыт на чтение.
     *  - Короткая запись для инстанциации значения.
    **/
    public function __get($name)
    {
        if ($name === 'view') {
            return $this->view;
        }
        return $this->from($name);
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
        // Поискать среди представлений.
        foreach ($this->view->each() as $view) {
            $value = $view->from($input);
            if ($value) {
                return $value;
            }
        }
        return null;
    }
    
    public function has($value)
    {
        if (! $value instanceof ValueInterface) {
            return false;
        }
        return $value->is($this);
    }
    
    public function toString($internal)
    {
        foreach ($this->view->each() as $view) {
            return $view->to($internal);
        }
    }
    
    public function value($internal)
    {
        return new Value($this, $internal);
    }
}
