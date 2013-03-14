<?php
namespace Zettai\Type\Type;

interface DereferenceableInterface extends TypeInterface
{
    public function dereference($internal, $offset);
    
    public function dereferenceExists($internal, $offset);
}
