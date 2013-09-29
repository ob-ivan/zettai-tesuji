<?php
/**
 * Model service provider.
 *
 * Parameters:
 *  [model.prefix]
 *      A string added to each table name.
 *      Intended to separate table namespaces, e.g.:
 *          - 'prod_' for production,
 *          - 'dev_'  for development,
 *          - 'test_' for testing,
 *          - and so on.
 *      Defaults to empty string.
 *
 * Dependencies:
 *  [config]
 *      Looks up for model.logger_enable config variable, and enables logging
 *      to [monolog] if it is true.
 *  [db]
 *      Doctrine DBAL service.
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
                isset($app['model.prefix']) ? $app['model.prefix'] : ''
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
