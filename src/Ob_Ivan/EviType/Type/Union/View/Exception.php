<?php
namespace Ob_Ivan\EviType\Type\Union\View;

use Ob_Ivan\EviType\Type\Union\Exception as ParentException;

class Exception extends ParentException
{
    const SELECT_EXPORT_INTERNAL_WRONG_TYPE     = 0x040101;
    const SELECT_EXPORT_VARIANT_NAME_UNKNOWN    = 0x040102;
    const SELECT_IMPORT_OPTIONS_WRONG_TYPE      = 0x040103;
}
