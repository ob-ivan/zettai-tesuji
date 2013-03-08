<?php
namespace Zettai\Type;

interface TypeInterface
{
    public function from($input);
    
    public function toViewByName($viewName, $primitive);
}
