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
    
    public function __construct(ServiceInterface $service, array $variants)
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
            foreach ($variant->each() as $primitive => $value) {
                $external = $this->pack($index, $primitive);
                $return[$external] = $this->fromPrimitive($external);
            }
        }
        return $return;
    }
    
    public function equals($a, $b)
    {
        foreach ($a as $index => $value) {
            if (! isset($b[$index])) {
                return false;
            }
            if (! $value->equals($b[$index])) {
                return false;
            }
        }
        foreach ($b as $index => $value) {
            if (! isset($a[$index])) {
                return false;
            }
            if (! $value->equals($a[$index])) {
                return false;
            }
        }
        return true;
    }
    
    public function fromPrimitive($external)
    {
        $map = json_decode($external);
        if (! (is_array($map) || is_object($map))) {
            return null;
        }
        foreach ($map as $index => $primitive) {
            if (isset($this->variants[$index])) {
                $internal = [$index => $this->variants[$index]->fromPrimitive($primitive)];
                return $this->value($internal);
            }
        }
        return null;
    }
    
    public function fromView($view, $presentation)
    {
        foreach ($this->variants as $index => $variant) {
            $candidate = $variant->fromView($view, $presentation);
            if ($candidate) {
                $internal = [$index => $candidate];
                return $this->value($internal);
            }
        }
        return null;
    }
    
    public function toView($view, $internal)
    {
        foreach ($internal as $index => $value) {
            return $value->toView($view);
        }
    }
    
    // private //
    
    private function pack($index, $primitive)
    {
        return json_encode([$index => $primitive]);
    }
}
