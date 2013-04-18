<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Ob_Ivan\Model\Service;
use Zettai\Entity\Exercise;
use Zettai\Entity\Theme;

class ModelServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['model'] = $app->share(function ($app) {
            return new Service($app['db'], $app['debug']);
        });
    }

    public function boot(Application $app)
    {
        if ($app['config']->model->logger_enable) {
            $app['model']->setLogger($app['monolog']);
        }
        $app['model']->register('exercise', function ($service) {
            return new Exercise($service);
        });
        $app['model']->register('theme', function ($service) use ($app) {
            return new Theme($service, $app['type']->theme);
        });
    }
}
