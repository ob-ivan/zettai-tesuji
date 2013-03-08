<?php
namespace Zettai\Type;

class Product extends Type
{
    // var //
    
    /**
     *  @var [<multiplierIndex> => <TypeInterface>]
    **/
    private $multipliers = [];
    
    // public //
    
    public function __construct (Service $service, array $multipliers)
    {
        parent::__construct($service);
        
        $this->multipliers = $multipliers;
    }
    
    public function each()
    {
        /**
         * [<multiplierIndex> => [<primitive>]]
        **/
        $primitives = [];
        /**
         * [<multiplierIndex> => <primitive values count>]
        **/
        $counts = [];
        $totalCount = 1;
        foreach ($this->multipliers as $multiplierIndex => $multiplier) {
            $primitives[$multiplierIndex] = array_keys($multiplier->each());
            $counts[$multiplierIndex] = count($primitives[$multiplierIndex]);
            $totalCount *= $counts[$multiplierIndex];
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
        foreach ($this->multipliers as $multiplierIndex => $multiplier) {
            if (! $multiplier->fromPrimitive($primitive[$multiplierIndex])) {
                return null;
            }
        }
        return new Value($this, $primitive);
    }
    
    public function fromView($view, $presentation)
    {
        // TODO: Научить типы откусывать от представления столько, сколько надо.
    }
    
    public function toView($view, $primitive)
    {
        $presentation = [];
        foreach ($this->multipliers as $multiplierIndex => $multiplier) {
            $presentation[] = $multiplier->toView($view, $primitive[$multiplierIndex]);
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
