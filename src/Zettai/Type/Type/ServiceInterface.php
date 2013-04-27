<?php
namespace Zettai\Type\Type;

use ArrayAccess;

interface ServiceInterface extends ArrayAccess
{
    public function from($input);
    
    public function register($typeName, $provider);
}
