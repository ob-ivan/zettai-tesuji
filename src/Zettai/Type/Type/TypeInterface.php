<?php
namespace Zettai\Type\Type;

interface TypeInterface
{
    public function from($input);
    
    public function has($value);
}
