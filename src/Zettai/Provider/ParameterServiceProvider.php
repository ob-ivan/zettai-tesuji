<?php
/**
 * Сервис для задания стандартных свойств параметров рутинга.
 *
 * Usage:
 *  $app->register(new ParameterServiceProvider(), [
 *      'parameter.options' => [
 *          'page' => [
 *              'assert'  => '\\d*',
 *              'value'   => '1',
 *              'convert' => function (...) { ... },
 *          ],
 *          ...
 *      ]
 *  ]);
 *
 *  $app['parameter']->setParameters(
 *      $app->get('/{page}', function (...) { ... }),
 *      ['page', ...]
 *  )
 *  ->bind(...);
**/
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Zettai\Parameter\Service;

class ParameterServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['parameter'] = new Service();
    }
    
    public function boot(Application $app)
    {
        $service = $app['parameter'];
        foreach ($app['parameter.options'] as $parameterName => $parameterOptions) {
            $service[$parameterName] = $parameterOptions;
        }
    }
}
