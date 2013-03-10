<?php
namespace Zettai\Type;

use ArrayAccess;

interface ValueInterface extends ArrayAccess
{
    public function __construct(TypeInterface $type, $primitive);
    
    public function is(TypeInterface $type);
    
    public function toView($view);
}
