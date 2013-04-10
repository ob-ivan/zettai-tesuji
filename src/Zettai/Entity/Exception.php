<?php
namespace Zettai\Entity;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const EXERCISE_ID_EMPTY     = 0x020101;
    const EXERCISE_TITLE_EMPTY  = 0x020102;

    const THEME_ID_EMPTY        = 0x020201;
}
