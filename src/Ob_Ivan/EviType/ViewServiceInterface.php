<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

interface ViewServiceInterface extends ArrayAccess
{
    public function register($name, callable $producer = null);
}
