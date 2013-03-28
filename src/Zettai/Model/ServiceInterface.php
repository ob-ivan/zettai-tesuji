<?php
namespace Zettai\Model;

use Doctrine\DBAL\Connection;

interface ServiceInterface
{
    public function __construct (Connection $db, $debug);
    
    public function getTableName(EntityInterface $entity);
    
    public function register($name, callable $entityProvider);
}
