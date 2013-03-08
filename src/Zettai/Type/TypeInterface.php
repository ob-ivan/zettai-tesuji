<?php
namespace Zettai\Type;

interface TypeInterface
{
    public function each();
    
    public function from($input);
    
    public function fromPrimitive($primitive);
    
    public function toViewByName($viewName, $primitive);
}
