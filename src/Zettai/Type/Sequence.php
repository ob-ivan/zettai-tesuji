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

class Sequence extends Type
{
    /**
     * Тип элемента последовательности.
     *
     *  @var TypeInterface
    **/
    private $element;
    
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
        $internal = [];
        foreach ($primitives as $index => $primitive) {
            $internal[$index] = $this->element->fromPrimitive($primitive);
        }
        return new Value($this, $internal);
    }
}
