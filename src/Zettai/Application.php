<?php
namespace Zettai;

use Silex\Provider\DoctrineServiceProvider;
use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    // include //
    
    use BaseApplication\MonologTrait;
    use BaseApplication\TwigTrait;
    use BaseApplication\UrlGeneratorTrait;
    
    // public //
    
    public function __construct(Config $config)
    {
        parent::__construct();
        
        // Зарегистрировать стандартные для zettai-приложения компоненты.
        $this->registerConfig($config);
        $this->registerDatabase();
        $this->registerModel();
    }
    
    // private //
    
    private function registerConfig (Config $config)
    {
        // Запомнить конфиг.
        $this['config'] = $config;
        
        // Применить поведение по умолчанию.
        if ($this['config']->debug) {
            $this['debug'] = true;
        }
    }
    
    private function registerDatabase ()
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
    
    private function registerModel()
    {
        $app = $this;
        $this['model'] = $this->share(function () use ($app) {
            return new Model($app['db']);
        });
    }
}
