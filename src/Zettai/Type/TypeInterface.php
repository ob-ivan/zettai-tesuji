<?php
namespace Zettai\Type;

interface TypeInterface
{
    public function each();
    
    public function from($input);
    
    public function fromPrimitive($primitive);
    
    public function has($value);
    
    public function toViewByName($viewName, $primitive);
}
