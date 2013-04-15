<?php
/**
 * Операции по обновлению базы данных при выгрузке или по крону.
**/
namespace Zettai\Controller\Console;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Import implements ControllerProviderInterface
{
    // public : ControllerProviderInterface //

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/is_answered', function () {
            return $this->is_answered();
        });
        return $controllers;
    }

    // private : controllers //

    /**
     * Проставляет всем задачам в базе флаг content.is_unasnwered значнием "истина".
    **/
    private function is_answered()
    {
    }
}
