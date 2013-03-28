<?php
namespace Zettai\Model;

use Doctrine\DBAL\Connection;

/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Документация: https://github.com/ob-ivan/zettai-tesuji/wiki/Data-Model
**/
class Service implements ServiceInterface
{
    private $db;
    private $debug;
    private $entities = [];
    private $registry = [];

    // public : ServiceInterface //
    
    public function register($name, callable $entityProvider)
    {
        $this->registry[$name] = $entityProvider;
    }
    
    // public : Service //
    
    public function __construct (Connection $db, $debug)
    {
        $this->db    = $db;
        $this->debug = $debug;
    }
    
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
}
