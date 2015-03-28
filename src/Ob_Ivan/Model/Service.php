<?php
/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Документация: https://github.com/ob-ivan/zettai-tesuji/wiki/Data-Model
**/
namespace Ob_Ivan\Model;

use Doctrine\DBAL\Connection;
use Monolog\Logger;

class Service implements ServiceInterface
{
    // var //

    private $db;

    private $logger;

    /**
     * A prefix added to each table name. Enables namespacing for table names
     * within single database.
     *
     *  @var string
    **/
    private $tablePrefix = '';

    private $entities = [];
    private $registry = [];

    // public : ServiceInterface //

    public function __construct(Connection $db, $tablePrefix = '')
    {
        $this->db           = $db;
        $this->tablePrefix  = $tablePrefix;
    }

    public function getTableName(EntityInterface $entity)
    {
        return $this->tablePrefix . $entity->getTableName();
    }

    public function queryBuilder(EntityInterface $entity)
    {
        return new QueryBuilder($this, $entity);
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

    public function fetchAll($query, array $parameters)
    {
        return $this->runTimedQuery(
            [$this->db, 'fetchAll'],
            __METHOD__,
            $query,
            $parameters
        );
    }

    public function fetchAssoc($query, array $parameters)
    {
        return $this->runTimedQuery(
            [$this->db, 'fetchAssoc'],
            __METHOD__,
            $query,
            $parameters
        );
    }

    public function fetchColumn($query, array $parameters)
    {
        return $this->runTimedQuery(
            [$this->db, 'fetchColumn'],
            __METHOD__,
            $query,
            $parameters
        );
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

    public function truncate($tableName)
    {
        // TODO: Escape tableName.
        return $this->db->executeQuery('TRUNCATE ' . $tableName);
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

    private function runTimedQuery(callable $execute, $label, $query, $parameters)
    {
        $start = microtime(true);
        $return = $execute($query, $parameters);
        $this->logQuery($label, $query, $parameters, microtime(true) - $start);
        return $return;
    }

    private function logQuery($method, $query, array $parameters, $duration = null)
    {
        if ($this->logger) {
            $message = $method . ': query = ' . $query . '; parameters = ' . var_export($parameters, true);
            if ($duration !== null)
            {
                $message .= '; duration = ' . sprintf('%.2f', $duration) . ' sec';
            }
            $this->logger->addInfo($message);
        }
    }
}
