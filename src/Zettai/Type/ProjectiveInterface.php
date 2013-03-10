<?php
namespace Zettai\Type;

interface ProjectiveInterface extends TypeInterface
{
    public function project($coordinate, $internal);
}
