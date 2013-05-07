<?php
namespace Zettai;

use Monolog\Logger;
use Silex\Provider\DoctrineServiceProvider,
    Silex\Provider\MonologServiceProvider;
use Silex\Application as BaseApplication;
use Zettai\Provider\ModelServiceProvider,
    Zettai\Provider\TypeServiceProvider;

class Application extends BaseApplication
{
    // include //

    use BaseApplication\MonologTrait;
    use BaseApplication\TwigTrait;
    use BaseApplication\UrlGeneratorTrait;

    // public //

    public function __construct($documentRoot)
    {
        parent::__construct();

        $this->documentRoot = $documentRoot;

        // Зарегистрировать стандартные для zettai-приложения компоненты.
        $this->registerConfig($this->documentRoot . '/config');
        $this->registerType();
        $this->registerDatabase();
        $this->registerModel();
        $this->registerMonolog($this->documentRoot . '/log');
    }

    // private //

    private function registerConfig ($configRoot)
    {
        // Запомнить конфиг.
        $this['config'] = new Config($configRoot);

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
        $this->register(new ModelServiceProvider());
    }

    private function registerMonolog()
    {
        $this->register(new MonologServiceProvider(), [
            'monolog.logfile'   => $this->documentRoot . '/log/' . (
                $this['debug']
                ? '/debug.log'
                : '/error.log'
            ),
            'monolog.level'     =>
                $this['debug']
                ? Logger::DEBUG
                : Logger::ERROR,
            'monolog.name'      => 'zettai-tesuji',
        ]);
    }

    private function registerType()
    {
        $this->register(new TypeServiceProvider());
    }
}
