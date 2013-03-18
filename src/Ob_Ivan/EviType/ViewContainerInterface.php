<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

interface ViewContainerInterface extends ArrayAccess
{
    public function register($name, callable $producer);
}
