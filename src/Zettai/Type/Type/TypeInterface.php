<?php
namespace Zettai\Type;

interface TypeInterface
{
    public function from($input);
    
    public function fromPrimitive($primitive);
    
    public function has($value);
    
    public function toPrimitive($internal);
    
    public function toViewByName($viewName, $primitive);
}
