<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

interface ServiceInterface extends ArrayAccess
{
    public function register($name, callable $producer);
}
