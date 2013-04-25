<?php
namespace Ob_Ivan\EviType\Type\Enum\View;

use ArrayAccess,
    Traversable;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Type\Enum\Internal;

class Dictionary implements ViewInterface
{
    private $map;

    /**
     *  @param  [<primitive> => <name>] $map
    **/
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

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::DICTIONARY_EXPORT_INTERNAL_WRONG_TYPE
            );
        }
        return $this->map[$internal->getPrimitive()];
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        foreach ($this->map as $primitive => $name) {
            if ($presentation === $name) {
                return new Internal($primitive);
            }
        }
    }
}
