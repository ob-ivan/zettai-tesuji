<?php
namespace Zettai\Model;

use Doctrine\DBAL\Connection;
use Monolog\Logger;

interface ServiceInterface
{
    public function __construct (Connection $db, $debug);
    
    public function getTableName(EntityInterface $entity);
    
    public function register($name, callable $entityProvider);
    
    public function setLogger(Logger $logger);
    
    // fetch //
    
    public function fetchAll($query, $parameters);
    
    public function fetchAssoc($query, $parameters);
    
    public function fetchColumn($query, $parameters);
    
    // modify //
    
    public function delete($tableName, $filter);
    
    public function insert($tableName, $data);
    
    public function update($tableName, $data, $filter);
}
