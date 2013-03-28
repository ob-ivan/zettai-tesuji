<?php
namespace Zettai\Model;

use Doctrine\DBAL\Connection;

interface ServiceInterface
{
    public function __construct (Connection $db, $debug);
    
    public function fetchAll($query, $parameters);
    
    public function fetchAssoc($query, $parameters);
    
    public function fetchColumn($query, $parameters);
    
    public function getTableName(EntityInterface $entity);
    
    public function register($name, callable $entityProvider);
}
