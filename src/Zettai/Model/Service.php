<?php
namespace Zettai\Model;

use Doctrine\DBAL\Connection;
use Monolog\Logger;

/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Документация: https://github.com/ob-ivan/zettai-tesuji/wiki/Data-Model
**/
class Service implements ServiceInterface
{
    // var //
    
    private $db;
    private $debug;
    private $logger;
    
    private $entities = [];
    private $registry = [];

    // public : ServiceInterface //
    
    public function __construct (Connection $db, $debug)
    {
        $this->db     = $db;
        $this->debug  = $debug;
    }
    
    public function getTableName(EntityInterface $entity)
    {
        return ($this->debug ? '_test_' : '') . $entity->getTableName();
    }
    
    public function register($name, callable $entityProvider)
    {
        $this->registry[$name] = $entityProvider;
    }
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    // public : ServiceInterface : fetch //
    
    public function fetchAll($query, $parameters)
    {
        $this->logQuery(__METHOD__, $query);
        return $this->db->fetchAll($query, $parameters);
    }
    
    public function fetchAssoc($query, $parameters)
    {
        $this->logQuery(__METHOD__, $query);
        return $this->db->fetchAssoc($query, $parameters);
    }
    
    public function fetchColumn($query, $parameters)
    {
        $this->logQuery(__METHOD__, $query);
        return $this->db->fetchColumn($query, $parameters);
    }
    
    // public : ServiceInterface : modify //
    
    public function delete($tableName, $filter)
    {
        return $this->db->delete($tableName, $filter);
    }
    
    public function insert($tableName, $data)
    {
        return $this->db->insert($tableName, $data);
    }
    
    public function update($tableName, $data, $filter)
    {
        return $this->db->update($tableName, $data, $filter);
    }
    
    // public : Service //
    
    public function __get($name)
    {
        if (! isset($this->entities[$name])) {
            if (! isset($this->registry[$name])) {
                throw new Exception('Unknown entity name "' . $name . '"', Exception::SERVICE_GET_NAME_UNKNOWN);
            }
            $this->entities[$name] = $this->registry[$name]($this);
            unset($this->registry[$name]);
        }
        return $this->entities[$name];
    }
    
    public function __isset($name)
    {
        return isset($this->entities[$name]) || isset($this->registry[$name]);
    }
    
    // private //
    
    private function logQuery($method, $query)
    {
        if ($this->logger) {
            $this->logger->addInfo($method . ': query = ' . $query);
        }
    }
}
