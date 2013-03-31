<?php
namespace Ob_Ivan\EviType;

use Ob_Ivan\EviType\Type;
use Ob_Ivan\EviType\TypeSortInterface;

class Enum implements TypeSortInterface
{
    public function produce($arguments)
    {
        $type = new Type($this);
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
    
    public function call($type, $method, $arguments)
    {
        switch ($method) {
            case 'each':
                // TODO
                break;
        }
    }
}
