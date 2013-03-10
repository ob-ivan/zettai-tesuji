<?php
namespace Zettai\Type;

class Map extends Type implements DereferenceableInterface
{
    // const //
    
    const INTERNAL_KEY_DOMAIN = 0;
    const INTERNAL_KEY_RANGE  = 1;
    
    // var //
    
    private $domain;
    private $range;
    
    // public : DereferenceableInterface //
    
    public function dereference($internal, $offset)
    {
        if (! $this->domain->has($offset)) {
            throw new Exception('Offset "' . $offset . '" is not in domain', Exception::MAP_DEREFERENCE_OFFSET_WRONG_DOMAIN);
        }
        return $internal[$offset->toPrimitive()][self::INTERNAL_KEY_RANGE];
    }
    
    public function dereferenceExists($internal, $offset)
    {
        if (! $this->domain->has($offset)) {
            throw new Exception('Offset "' . $offset . '" is not in domain', Exception::MAP_DEREFERENCE_OFFSET_WRONG_DOMAIN);
        }
        return isset($internal[$offset->toPrimitive()]);
    }
    
    // public : Map //
    
    public function __construct(ServiceInterface $service, $domain, $range)
    {
        parent::__construct($service);
        
        $this->domain = $service->type($domain);
        $this->range  = $service->type($range);
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
        foreach ($array as $domainPresentation => $rangePresentation) {
            $domainValue = $this->domain->from($domainPresentation);
            if (! $domainValue) {
                return null;
            }
            $rangeValue = $this->range->from($rangePresentation);
            if (! $rangeValue) {
                return null;
            }
            $internal[$domainValue->toPrimitive()] = [
                self::INTERNAL_KEY_DOMAIN => $domainValue,
                self::INTERNAL_KEY_RANGE  => $rangeValue,
            ];
        }
        return $this->value($internal);
    }
    
    public function fromPrimitive($primitive)
    {
        if (! is_string($primitive)) {
            return null;
        }
        $primitiveMap = json_decode($primitive);
        if (! (is_array($primitiveMap) || is_object($primitiveMap))) {
            return null;
        }
        $internal = [];
        foreach ($primitiveMap as $domainPrimitive => $rangePrimitive) {
            $domainValue = $this->domain->fromPrimitive($domainPrimitive);
            if (! $domainValue) {
                return null;
            }
            $rangeValue = $this->rage->fromPrimitive($rangePrimitive);
            if (! $rangeValue) {
                return null;
            }
            $internal[$domainPrimitive] = [
                self::INTERNAL_KEY_DOMAIN => $domainValue,
                self::INTERNAL_KEY_RANGE  => $rangeValue,
            ];
        }
        return $this->value($internal);
    }
    
    public function fromView($view, $presentation)
    {
        // Какое могло бы быть естественное представление у отображения?
        return null;
    }
    
    public function toPrimitive($internal)
    {
        $primitiveMap = [];
        foreach ($internal as $domainPrimitive => $pair) {
            $primitiveMap[$domainPrimitive] = $pair[self::INTERNAL_KEY_RANGE]->toPrimitive();
        }
    }
    
    public function toView($view, $internal)
    {
        // Какое могло бы быть естественное представление у отображения?
        return null;
    }
}
