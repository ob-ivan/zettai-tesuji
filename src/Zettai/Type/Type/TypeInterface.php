<?php
namespace Zettai\Type\Type;

interface TypeInterface
{
    public function equals($internalA, $internalB);
    
    public function from($input);
    
    public function has($value);
}
