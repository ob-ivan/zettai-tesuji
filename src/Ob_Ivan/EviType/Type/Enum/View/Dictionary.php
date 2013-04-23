<?php
namespace Ob_Ivan\EviType\Type\Enum\View;

use ArrayAccess, Traversable;
use Ob_Ivan\EviType\Enum\Internal;
use Ob_Ivan\EviType\InternalInterface;
use Ob_Ivan\EviType\ViewInterface;

class Dictionary implements ViewInterface
{
    private $map;

    public function __construct($map)
    {
        if (! (is_array($map) || ($map instanceof ArrayAccess && $map instanceof Traversable))) {
            throw new Exception(
                'Map must be an array or implement array-like behaviour',
                Exception::DICTIONARY_CONSTRUCT_MAP_WRONG_TYPE
            );
        }
        $this->map = $map;
    }

    public function export(InternalInterface $internal)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::DICTIONARY_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return $this->map[$internal->getPrimitive()];
    }

    public function import($presentation)
    {
        foreach ($this->map as $primitive => $name) {
            if ($presentation === $name) {
                return new Internal($primitive);
            }
        }
    }
}
