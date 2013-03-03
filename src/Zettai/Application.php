<?php
namespace Zettai;

use Silex\Provider\DoctrineServiceProvider;
use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    // include //
    
    use BaseApplication\TwigTrait;
    use BaseApplication\UrlGeneratorTrait;
    
    // public //
    
    public function registerConfig (Config $config)
    {
        // Запомнить конфиг.
        $this['config'] = $config;
        
        // Применить поведение по умолчанию.
        if ($this['config']->debug) {
            $this['debug'] = true;
        }
    }
    
    public function registerDatabase ()
    {
        $this->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver'    => 'pdo_mysql',
                'host'      => $this['config']->db->host,
                'dbname'    => $this['config']->db->dbname,
                'user'      => $this['config']->db->user,
                'password'  => $this['config']->db->password,
                'charset'   => 'utf8',
            ],
        ]);
    }
}
