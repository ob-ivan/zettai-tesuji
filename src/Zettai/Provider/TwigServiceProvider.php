<?php
namespace Zettai\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider as WrappedProvider;
use Twig_Filter_Function, Twig_SimpleFilter, Twig_SimpleFunction;

class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new WrappedProvider());
    }
    
    public function boot(Application $app)
    {
        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

            // фильтры : обычные //
            
            $twig->addFilter(new Twig_SimpleFilter('answer', function ($source) use ($app) {
                return $app['answer_compiler']->compile($source);
            }));
            $twig->addFilter(new Twig_SimpleFilter('lpad', function ($input, $char, $length) {
                return str_pad($input, $length, $char, STR_PAD_LEFT);
            }));
            
            // фильтры : типы //
            
            $twig->addFilter('wind', new Twig_Filter_Function(function ($wind) use ($app) {
                return $app['types']->wind->from($wind)->toRussian();
            }));
            $twig->addFilter('kyoku', new Twig_Filter_Function(function ($kyoku) use ($app) {
                return $app['types']->kyoku->from($kyoku)->toRussian();
            }));
            $twig->addFilter('tile', new Twig_Filter_Function(function ($tiles) use ($app) {
                return $app['twig']->render('_tile.twig', ['tiles' => $tiles]);
            }));
            
            // функции //
            
            $twig->addFunction(new Twig_SimpleFunction('ceil',  function ($float) { return ceil  ($float); }));
            $twig->addFunction(new Twig_SimpleFunction('floor', function ($float) { return floor ($float); }));
            $twig->addFunction(new Twig_SimpleFunction('max',   function ($a, $b) { return max   ($a, $b); }));
            $twig->addFunction(new Twig_SimpleFunction('min',   function ($a, $b) { return min   ($a, $b); }));
            
            return $twig;
        }));
    }
}
