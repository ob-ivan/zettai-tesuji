<?php
namespace Ob_Ivan\EviType\TypeSort;

use Ob_Ivan\EviType\Type;
use Ob_Ivan\EviType\TypeSortInterface;
use Ob_Ivan\EviType\ViewService;

class Enum implements TypeSortInterface
{
    public function __construct()
    {
        $this->view = new ViewService();
    }

    public function produce(array $arguments)
    {
        $type = new Type($this, $arguments);

        /*
        $type->to('Default', function ($internal) use ($arguments) {
            return $arguments[$internal];
        });
        $type->from('Default', function ($presentation) use ($arguments) {
            $internal = array_search($presentation, $arguments);
            if ($internal === false) {
                return null;
            }
            return $internal;
        });
        */

        return $type;
    }

    public function call(Type $type, $method, array $arguments)
    {
        switch ($method) {
            case 'each':
                // TODO
                break;
        }
    }

    public function view(Type $type)
    {
        return new ViewService($this->view);
    }
}
