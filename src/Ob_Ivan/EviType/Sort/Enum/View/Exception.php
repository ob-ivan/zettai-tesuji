<?php
namespace Ob_Ivan\EviType\Sort\Enum\View;

use Ob_Ivan\EviType\Sort\Enum\Exception as ParentException;

class Exception extends ParentException
{
    const DICTIONARY_CONSTRUCT_MAP_WRONG_TYPE   = 0x040101;
    const DICTIONARY_EXPORT_INTERNAL_WRONG_TYPE = 0x040102;
}
