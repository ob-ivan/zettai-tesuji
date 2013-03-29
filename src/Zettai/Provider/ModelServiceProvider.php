<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Zettai\Model\Exercise;
use Zettai\Model\Service;

class ModelServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['model'] = new Service($app['db'], $app['debug']);
    }
    
    public function boot(Application $app)
    {
        if ($app['config']->model->logger_enable) {
            $app['model']->setLogger($app['monolog']);
        }
        $app['model']->register('exercise', function ($service) {
            return new Exercise($service);
        });
    }
}
