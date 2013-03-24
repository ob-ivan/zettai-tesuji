<?php
namespace Zettai;

use ArrayAccess;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Загружает конфиг проекта.
 * 
 * Конфиг берётся из файла {$rootDirectory}/config/config.yml.
 * К переменным конфига можно обращаться через стрелочку (->).
 * Если переменная не определена в конфиге, бросается исключение
 * Zettai\Exception::CONFIG_VARIABLE_UNKNOWN.
**/
class Config implements ArrayAccess
{
    // var //
    
    private $rootDirectory;
    private $isLoaded = false;
    private $configValues = null;
    
    // public : ArrayAccess //

    public function offsetExists($offset)
    {
        return isset($this->configValues[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
    
    public function offsetSet ($offset, $value)
    {
        throw new Exception ('Config is read only', Exception::CONFIG_READ_ONLY);
    }
    
    public function offsetUnset ($offset)
    {
        throw new Exception ('Config is read only', Exception::CONFIG_READ_ONLY);
    }
    
    // public : Config //
    
    public function __construct ($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function __get ($name)
    {
        if (! $this->isLoaded) {
            $this->load();
        }
        if (isset($this->configValues[$name])) {
            $value = $this->configValues[$name];
            if (is_array($value) || is_object($value)) {
                $subconfig = new Config(null);
                $subconfig->configValues = $value;
                $subconfig->isLoaded = true;
                return $subconfig;
            }
            return $value;
        }
        throw new Exception('Config variable "' . $name . '" is unknown', Exception::CONFIG_VARIABLE_UNKNOWN);
    }
    
    public function __set ($name, $value)
    {
        throw new Exception ('Config is read only', Exception::CONFIG_READ_ONLY);
    }
    
    public function toArray()
    {
        $return = [];
        foreach ($this->configValues as $field => $value) {
            $return[$field] = $value;
        }
        return $return;
    }
    
    // private //
    
    private function load()
    {
        if ($this->isLoaded) {
            return;
        }
        $locator = new FileLocator($this->rootDirectory . '/config');
        $resource = $locator->locate('config.yml');
        $this->configValues = Yaml::parse($resource);
        $this->isLoaded = true;
    }
}

