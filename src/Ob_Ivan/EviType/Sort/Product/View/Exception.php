<?php
namespace Ob_Ivan\EviType\Sort\Product\View;

use Ob_Ivan\EviType\Sort\Product\Exception as ParentException;

class Exception extends ParentException
{
    const SEPARATOR_CONSTRUCT_MAP_WRONG_TYPE   = 0x040101;
    const SEPARATOR_EXPORT_INTERNAL_WRONG_TYPE = 0x040102;
    const SEPARATOR_IMPORT_OPTIONS_WRONG_TYPE  = 0x040103;
}
