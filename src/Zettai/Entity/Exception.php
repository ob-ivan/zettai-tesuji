<?php
namespace Zettai\Entity;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const ID_EMPTY     = 0x020101;
    const TITLE_EMPTY  = 0x020102;
}
