<?php
namespace Zettai\Model;

interface ServiceInterface
{
    public function register($name, callable $entityProvider);
}
