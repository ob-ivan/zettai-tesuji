<?php
namespace Zettai\Model;

interface ServiceInterface
{
    public function register($name, callable $entityProvider);
    
    public function execute(QueryBuilder $queryBuilder, EntityInterface $entity);
}
