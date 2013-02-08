<?php
namespace Zettai;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Загружает и отдаёт конфиг для проекта.
 * Построен на компонентах Symfony.
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

