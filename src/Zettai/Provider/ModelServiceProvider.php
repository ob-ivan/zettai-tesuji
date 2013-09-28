<?php
/**
 * Model service provider.
 *
 * Dependencies:
 *  [config]
 *      Looks up for model.logger_enable config variable, and enables logginh
 *      to [monolog] if it is true.
 *  [db]
 *      Doctrine service.
 *  [debug]
 *      True when in development, false when in production.
 *  [monolog]
 *      Monolog service.
 *
 * Services:
 *  [model]
 *      Instance of Model\Service.
**/
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
            return new Service(
                $app['db'],
                $app['debug'] ? '_test_' : ''
            );
        });
    }

    public function boot(Application $app)
    {
        if ($app['config']->model->logger_enable) {
            $app['model']->setLogger($app['monolog']);
        }
        $app['model']->register('exercise', function ($service) use ($app) {
            return new Exercise($service, $app['types']->exercise);
        });
        $app['model']->register('theme', function ($service) use ($app) {
            return new Theme($service, $app['types']->theme);
        });
    }
}
