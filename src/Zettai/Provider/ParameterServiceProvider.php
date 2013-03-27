<?php
/**
 * Поставщик сервиса для задания стандартных свойств параметров рутинга.
 *
 * Зарегистрировать сервис:
 *  $app->register(new ParameterServiceProvider(), [
 *      'parameter.rules' => [
 *          'positive' => [                             // Название правила.
 *              'assert'  => '\\d*',                    // Методы, которые надо вызвать на контроллере,
 *              'value'   => '1',                       // и их параметры.
 *              'convert' => function (...) { ... },
 *          ],
 *          ...
 *      ]
 *  ]);
 *
 * Использовать сервис:
 *  $app['parameter']->setParameters(
 *      $app->get('/{page}/{...}', function (...) { ... }),
 *      [
 *          'page' => 'positive',                       // Название параметра и название применяемого к нему правила.
 *          ...    => ...,
 *      ]
 *  )
 *  ->bind(...);
**/
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Zettai\Parameter\Service;

class ParameterServiceProvider implements ServiceProviderInterface
{
    private $rules;
    
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }
    
    public function register(Application $app)
    {
        $service = new Service();
        foreach ($this->rules as $ruleName => $ruleOptions) {
            $service[$ruleName] = $ruleOptions;
        }
        $app['parameter'] = $service;
    }
    
    public function boot(Application $app)
    {
    }
}
