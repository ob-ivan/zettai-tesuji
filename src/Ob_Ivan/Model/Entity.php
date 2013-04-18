<?php
namespace Ob_Ivan\Model;

abstract class Entity implements EntityInterface
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    protected function queryBuilder()
    {
        return $this->service->queryBuilder($this);
    }
}
