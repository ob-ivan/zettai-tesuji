<?php
namespace Zettai\Type\View;

use Zettai\Type\ValueInterface;

interface ViewInterface
{
    public function from($input);
    
    public function to(ValueInterface $value);
}
