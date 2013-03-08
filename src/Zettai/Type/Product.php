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
            $return[$primitive] = $this->fromPrimitive($primitive);
        }
        return $return;
    }
    
    public function equals ($a, $b)
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
    
    public function fromPrimitive($primitive)
    {
        $values = [];
        $primitives = json_decode($primitive);
        foreach ($this->multipliers as $index => $multiplier) {
            $value = $multiplier->fromPrimitive($primitives[$index]);
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
        ksort($key, SORT_NUMERIC);
        return json_encode($key);
    }
}
