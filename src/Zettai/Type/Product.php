<?php
namespace Zettai\Type;

class Product extends Type
{
    // var //
    
    /**
     *  @var [<index> => <TypeInterface>]
    **/
    private $multipliers = [];
    
    // public //
    
    public function __construct (Service $service, array $multipliers)
    {
        parent::__construct($service);
        
        foreach ($multipliers as $index => $multiplier) {
            $this->multipliers[$index] = $service->type($multiplier);
        }
    }
    
    public function each()
    {
        /**
         * [<index> => [<primitive>]]
        **/
        $primitives = [];
        /**
         * [<index> => <primitive values count>]
        **/
        $counts = [];
        $totalCount = 1;
        foreach ($this->multipliers as $index => $multiplier) {
            $primitives[$index] = array_keys($multiplier->each());
            $counts[$index] = count($primitives[$index]);
            $totalCount *= $counts[$index];
        }
        $return = [];
        for ($seed = 0; $seed < $totalCount; ++$seed) {
            $primitive = $this->getKey($seed, $counts);
            $return[implode('-', $primitive)] = $this->fromPrimitive($primitive);
        }
        return $return;
    }
    
    public function fromPrimitive($primitive)
    {
        $values = [];
        foreach ($this->multipliers as $index => $multiplier) {
            $value = $multiplier->fromPrimitive($primitive[$index]);
            if (! $value) {
                return null;
            }
            $values[$index] = $value;
        }
        return new Value($this, $values);
    }
    
    public function fromView($view, $presentation)
    {
        $values = [];
        foreach ($this->multipliers as $index => $multiplier) {
            $value = $multiplier->fromView($view, $presentation);
            if (! $value) {
                return null;
            }
            $values[$index] = $value;
            $presentation = substr($presentation, strlen($value->toView($view)));
        }
        return new Value($this, $values);
    }
    
    public function toView($view, $values)
    {
        $presentation = [];
        foreach ($values as $index => $value) {
            $presentation[] = $value->toView($view);
        }
        return implode('', $presentation);
    }
    
    // private //
    
    /**
     *  @return [<index> => <0 .. counts[index]>]
    **/
    private function getKey($seed, $counts)
    {
        $key = [];
        for ($i = count($counts) - 1; $i >= 0; --$i) {
            $key[$i] = $seed % $counts[$i];
            $seed -= $key[$i];
            $seed /= $counts[$i];
        }
        return $key;
    }
}
