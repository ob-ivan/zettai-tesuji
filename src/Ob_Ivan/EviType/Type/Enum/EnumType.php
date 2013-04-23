<?php
namespace Ob_Ivan\EviType\Type\Enum;

use Ob_Ivan\EviType\Type;

class EnumType extends Type
{
    private $options;

    public function __construct(EnumTypeOptions $options)
    {
        $this->options = $options;
    }
}
