<?php
namespace Zettai\Type\Type;

use ArrayAccess;

interface ServiceInterface extends ArrayAccess
{
    public function register($typeName, $provider);
}
