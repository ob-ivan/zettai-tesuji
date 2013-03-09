<?php
/**
 * Объединение типов.
 *
 * Внешним примитивным значением является джсон вида '{"I":P}',
 * где I - индекс подтипа, P - его примитивное значение.
 *
 * Внутренним примитивным типом является массив из одного элемента
 * [<index> => <Value>].
**/
namespace Zettai\Type;

class Union extends Type
{
    /**
     *  @var [<index> => <TypeInterface>]
    **/
    private $variants;
    
    public function __construct(Service $service, array $variants)
    {
        parent::__construct($service);
        
        foreach ($variants as $index => $variant) {
            $this->variants[$index] = $service->type($variant);
        }
    }
    
    public function each()
    {
        $return = [];
        foreach ($this->variants as $index => $variant) {
            foreach ($variant->each as $subprimitive => $value) {
                $primitive = $this->pack($index, $subprimitive);
                $return[$primitive] = $this->fromPrimitive($primitive);
            }
        }
        return $return;
    }
    
    public function fromPrimitive($primitive)
    {
        $map = json_decode($primitive);
        foreach ($map as $index => $subprimitive) {
            if (isset($this->variants[$index])) {
                return new Value($this, [$index => $this->variants->fromPrimitive($subprimitive)]);
            }
        }
        return null;
    }
    
    // private //
    
    private function pack($index, $primitive)
    {
        return json_encode([$index => $primitive]);
    }
}
