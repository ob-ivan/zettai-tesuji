<?php
namespace Zettai\Controller;

use Zettai\Exception as ParentException;

class Exception extends ParentException
{
    const IMPORT_FILENAME_EMPTY   = 0x0201;
    const IMPORT_FILE_UNREACHABLE = 0x0202;
    const THEME_ID_INCONVERTIBLE  = 0x0203;
}
