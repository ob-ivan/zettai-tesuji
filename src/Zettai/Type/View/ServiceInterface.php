<?php
namespace Zettai\Type\View;

use ArrayAccess;

interface ServiceInterface extends ArrayAccess
{
    public function each();
    
    public function register($viewName, $provider);
}
