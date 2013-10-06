<?php
namespace Ob_Ivan\EviType\Sort\Product\View;

use Ob_Ivan\EviType\Sort\Product\Exception as ParentException;

class Exception extends ParentException
{
    const ASSOCIATIVE_CONSTRUCT_MAP_WRONG_TYPE          = 0x040101;
    const ASSOCIATIVE_EXPORT_INTERNAL_WRONG_TYPE        = 0x040102;
    const ASSOCIATIVE_IMPORT_PRESENTATION_WRONG_TYPE    = 0x040103;
    const ASSOCIATIVE_IMPORT_OPTIONS_WRONG_TYPE         = 0x040104;

    const SEPARATOR_CONSTRUCT_MAP_WRONG_TYPE            = 0x040201;
    const SEPARATOR_EXPORT_INTERNAL_WRONG_TYPE          = 0x040202;
    const SEPARATOR_IMPORT_OPTIONS_WRONG_TYPE           = 0x040203;
}
