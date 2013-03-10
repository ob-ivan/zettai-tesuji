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

class Product extends Type implements DereferenceableInterface
{
    // var //
    
    /**
     *  @var [<index> => <TypeInterface>]
    **/
    private $multipliers = [];
    
    // public : DereferenceableInterface //
    
    public function dereference($internal, $offset)
    {
        if (! isset($this->multipliers[$offset])) {
            throw new Exception('Unknown offset "' . $offset . '" for this type', Exception::PRODUCT_DEREFERENCE_OFFSET_UNKNOWN);
        }
        return $internal[$offset];
    }
    
    public function dereferenceExists($internal, $offset)
    {
        if (! isset($this->multipliers[$offset])) {
            throw new Exception('Unknown offset "' . $offset . '" for this type', Exception::PRODUCT_DEREFERENCE_OFFSET_UNKNOWN);
        }
        return isset($internal[$offset]);
    }
    
    // public : Product //
    
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
    
    /**
     * Дополнительно рассматривает $input как массив.
    **/
    public function from($input)
    {
        $candidate = parent::from($input);
        if ($candidate) {
            return $candidate;
        }
        if (is_array($input)) {
            return $this->fromArray($input);
        }
        return null;
    }
    
    public function fromArray($array)
    {
        $internal = [];
        foreach ($this->multipliers as $index => $multiplier) {
            if (! isset ($array[$index])) {
                return null;
            }
            $value = $multiplier->from($array[$index]);
            if (! $value) {
                return null;
            }
            $internal[$index] = $value;
        }
        return $this->value($internal);
    }
    
    public function fromPrimitive($primitive)
    {
        $internal = [];
        $primitives = json_decode($primitive);
        if (! (is_array($primitives) || is_object($primitives))) {
            return null;
        }
        foreach ($this->multipliers as $index => $multiplier) {
            $value = $multiplier->fromPrimitive($primitives[$index]);
            if (! $value) {
                return null;
            }
            $internal[$index] = $value;
        }
        return $this->value($internal);
    }
    
    public function fromView($view, $presentation)
    {
        $internal = [];
        foreach ($this->multipliers as $index => $multiplier) {
            $value = $multiplier->fromView($view, $presentation);
            if (! $value) {
                return null;
            }
            $internal[$index] = $value;
            $presentation = substr($presentation, strlen($value->toView($view)));
        }
        return $this->value($internal);
    }
    
    public function toPrimitive($internal)
    {
        $primitives = [];
        foreach ($this->multipliers as $index => $multiplier) {
            $primitives[$index] = $internal[$index]->toPrimitive();
        }
        return json_encode($primitives);
    }
    
    public function toView($view, $internal)
    {
        $presentation = [];
        foreach ($internal as $index => $value) {
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
