<?php
namespace Zettai\Model;

abstract class Entity implements EntityInterface
{
    private $service;
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
    
    protected function queryBuilder()
    {
        return new QueryBuilder($this->service, $this);
    }
}
