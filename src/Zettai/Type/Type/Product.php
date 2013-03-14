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
namespace Zettai\Type\Type;

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
    
    public function __construct (ServiceInterface $typeService, array $multipliers)
    {
        parent::__construct($typeService);
        
        foreach ($multipliers as $index => $multiplier) {
            $this->multipliers[$index] = $typeService->from($multiplier);
        }
        
        $this->view->register('array', 'phpArray');
    }
    
    /**
     * Перебирает все значения для каждой координаты и возвращает
     * по значению для всевозможных сочетаний координат.
     *
     *  TODO: Переделать без деления с остатком, ведь достаточно увеличивать
     *      последнюю координату и перекидывать в следующий разряд при переполнении.
     *
     *  @return [<Value>]
    **/
    public function each()
    {
        /**
         * [<index> => [<value>]]
        **/
        $values = [];
        /**
         * [<index> => <values count>]
        **/
        $counts = [];
        $totalCount = 1;
        foreach ($this->multipliers as $index => $multiplier) {
            $values[$index] = $multiplier->each();
            $counts[$index] = count($values[$index]);
            $totalCount *= $counts[$index];
        }
        $return = [];
        for ($seed = 0; $seed < $totalCount; ++$seed) {
            $return[] = $this->fromSeed($seed, $counts, $values);
        }
        return $return;
    }
    
    public function equals ($internalA, $internalB)
    {
        foreach ($internalA as $index => $value) {
            if (! isset($internalB[$index])) {
                return false;
            }
            if (! $value->equals($internalB[$index])) {
                return false;
            }
        }
        foreach ($internalB as $index => $value) {
            if (! isset($internalA[$index])) {
                return false;
            }
            if (! $value->equals($internalA[$index])) {
                return false;
            }
        }
        return true;
    }
    
    // private //
    
    /**
     *  @return Value
    **/
    private function fromSeed($seed, $counts, $values)
    {
        $internal = [];
        for ($i = count($counts) - 1; $i >= 0; --$i) {
            $rem = $seed % $counts[$i];
            $seed -= $rem;
            $seed /= $counts[$i];
            $internal[$i] = $values[$i][$rem];
        }
        return $this->value($internal);
    }
}
