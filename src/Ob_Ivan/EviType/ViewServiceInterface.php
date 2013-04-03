<?php
namespace Ob_Ivan\EviType;

use ArrayAccess;
use IteratorAggregate;

interface ViewServiceInterface extends ArrayAccess, IteratorAggregate
{
    public function register($name, callable $producer = null);
}
