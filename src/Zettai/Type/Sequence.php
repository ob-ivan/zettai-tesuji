<?php
/**
 * Тип конечных последовательностей из элементов одного типа.
 *
 * Примитивным кодом (внешним примитивным значением) является
 * json от массива [<index> => <primitive>, ...].
 *
 * Внутренним простым значением является массив [<index> => <value>, ...].
**/
namespace Zettai\Type;

class Sequence extends Type implements ProjectiveInterface
{
    // include //
    
    use HookTrait;
    
    // var //
    
    /**
     * Тип элемента последовательности.
     *
     *  @var TypeInterface
    **/
    private $element;
    
    // public //
    
    public function __construct(Service $service, $element)
    {
        parent::__construct($service);
        
        $this->element = $service->type($element);
    }
    
    public function equals($a, $b)
    {
        if (count($a) !== count($b)) {
            return false;
        }
        foreach ($a as $index => $value) {
            if (! $value->equals($b[$index])) {
                return false;
            }
        }
        return true;
    }
    
    public function fromPrimitive($external)
    {
        $primitives = json_decode($external);
        if (! (is_array($primitives) || is_object($primitives))) {
            return null;
        }
        $internal = [];
        foreach ($primitives as $index => $primitive) {
            $internal[$index] = $this->element->fromPrimitive($primitive);
        }
        return $this->value($internal);
    }
    
    public function fromView($view, $presentation)
    {
        if ($this->hasHook(__FUNCTION__)) {
            return $this->callHook(__FUNCTION__, func_get_args());
        }
        
        $internal = [];
        while (! empty($presentation)) {
            $candidate = $this->element->fromView($view, $presentation);
            if (! $candidate) {
                break;
            }
            $internal[] = $candidate;
            $prevLength = strlen($presentation);
            $presentation = substr($presentation, strlen($candidate->toView($view)));
            if ($prevLength <= strlen($presentation)) {
                break;
            }
        }
        return $this->value($internal);
    }
    
    public function project($coordinate, $internal)
    {
        if (! isset($internal[$coordinate])) {
            return null;
        }
        return $internal[$coordinate];
    }
    
    public function toView($view, $internal)
    {
        if ($this->hasHook(__FUNCTION__)) {
            return $this->callHook(__FUNCTION__, func_get_args());
        }
        
        $presentations = [];
        foreach ($internal as $index => $value) {
            $presentations[] = $value->toView($view);
        }
        $presentation = implode('', $presentations);
        return $presentation;
    }
}
