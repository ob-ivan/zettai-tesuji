<?php
/**
 * Декартово произведение типов.
 *
 * Внешним примитивным значением (ключами в each и на входе fromPrimitive)
 * являются строки вида '[X,Y,Z]', где X, Y, X - примитивные значения координат.
 *
 * Внутренним примитивным значением (хранимым в Value) является массив
 * [<index> => <Value>].
**/
namespace Zettai\Type;

class Product extends Type implements ProjectiveInterface
{
    // var //
    
    /**
     *  @var [<index> => <TypeInterface>]
    **/
    private $multipliers = [];
    
    // public //
    
    public function __construct (ServiceInterface $service, array $multipliers)
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
            $primitive = $this->pack($seed, $counts, $primitives);
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
        return $this->value($values);
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
        return $this->value($values);
    }
    
    public function project($coordinate, $internal)
    {
        if (! isset($this->multipliers[$coordinate])) {
            throw new Exception('Unknown coordinate "' . $coordinate . '" for this type', Exception::PRODUCT_PROJECT_COORDINATE_UNKNOWN);
        }
        return $internal[$coordinate];
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
     *  @return [<index> => primitives[index][0 .. counts[index]]]
    **/
    private function pack($seed, $counts, $primitives)
    {
        $key = [];
        for ($i = count($counts) - 1; $i >= 0; --$i) {
            $rem = $seed % $counts[$i];
            $key[$i] = $primitives[$i][$rem];
            $seed -= $key[$i];
            $seed /= $counts[$i];
        }
        ksort($key, SORT_NUMERIC);
        return json_encode($key);
    }
}
