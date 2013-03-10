<?php
namespace Zettai\Type;

class Map extends Type
{
    private $domain;
    private $range;
    
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
            $rangeValue = $this->rage->from($rangePresentation);
            if (! $rangeValue) {
                return null;
            }
            $internal[$domainValue->toPrimitive()] = $rangeValue;
        }
        return $this->value($internal);
    }
    
    public function fromPrimitive($primitive)
    {
        // Нет пока нужды реализовывать.
        return null;
    }
}
