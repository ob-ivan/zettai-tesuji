<?php
namespace Zettai\Type\Type;

interface TypeInterface
{
    public function equals($a, $b);
    
    public function from($input);
    
    public function has($value);
}
