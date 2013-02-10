<?php
namespace Zettai;

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
class Config
{
    // var //
    
    private $rootDirectory;
    private $isLoaded = false;
    private $configValues = null;
    
    // public //
    
    public function __construct ($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function __get ($name)
    {
        if (! $this->isLoaded) {
            $this->load();
        }
        if (isset ($this->configValues[$name])) {
            return $this->configValues[$name];
        }
        throw new Exception('Config variable "' . $name . '" is unknown', Exception::CONFIG_VARIABLE_UNKNOWN);
    }
    
    public function __set ($name, $value)
    {
        throw new Exception ('Config is read only', Exception::CONFIG_READ_ONLY);
    }
    
    // private //
    
    private function load()
    {
        if ($this->isLoaded) {
            return;
        }
        $locator = new FileLocator($this->rootDirectory . '/config');
        $resource = $locator->locate('config.yml');
        $this->configValues = new ArrayObject (Yaml::parse($resource), ArrayObject::READ_ONLY);
        $this->isLoaded = true;
    }
}

