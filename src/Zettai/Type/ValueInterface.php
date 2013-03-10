<?php
namespace Zettai\Type;

interface ValueInterface
{
    public function __construct(TypeInterface $type, $primitive);
    
    public function is(TypeInterface $type);
    
    public function toView($view);
}
