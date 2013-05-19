<?php
namespace Ob_Ivan\EviType\Type\Product\Builder;

use Ob_Ivan\EviType\Type\Product\Exception as ParentException;

class Exception extends ParentException
{
    const RECORD_IMPORT_ARRAY_COMPONENT_MISSING     = 0x040101;
    const RECORD_IMPORT_ARRAY_COMPONENT_WRONG_TYPE  = 0x040102;
}
