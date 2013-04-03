<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;

interface TypeServiceInterface extends ArrayAccess
{
    public function register($name, callable $producer = null);
}
