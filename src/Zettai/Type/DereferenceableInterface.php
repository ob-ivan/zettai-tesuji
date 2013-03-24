<?php
namespace Zettai\Type;

interface DereferenceableInterface extends TypeInterface
{
    public function dereference($internal, $offset);
    
    public function dereferenceExists($internal, $offset);
}
