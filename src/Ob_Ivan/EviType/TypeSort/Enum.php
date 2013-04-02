<?php
namespace Ob_Ivan\EviType\TypeSort;

use Ob_Ivan\EviType\Type;
use Ob_Ivan\EviType\TypeSortInterface;
use Ob_Ivan\EviType\ViewService;

class Enum implements TypeSortInterface
{
    public function produce(array $arguments)
    {
        $type = new Type($this, $arguments);
        
        $type->to('Default', function ($primitive) use ($arguments) {
            return $arguments[$primitive];
        });
        $type->from('Default', function ($presentation) use ($arguments) {
            $primitive = array_search($presentation, $arguments);
            if ($primitive === false) {
                return null;
            }
            return $primitive;
        });
        
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
        $viewService = new ViewService($type);
        return $viewService;
    }
}
