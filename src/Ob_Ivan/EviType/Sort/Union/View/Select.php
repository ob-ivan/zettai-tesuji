<?php
/**
 * Представление, сопоставляющее каждому варианту своё подпредставление.
**/
namespace Ob_Ivan\EviType\Sort\Union\View;

use Ob_Ivan\EviType\Exception as PackageException;
use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;
use Ob_Ivan\EviType\Sort\Union\Internal,
    Ob_Ivan\EviType\Sort\Union\Options;

class Select implements ViewInterface
{
    private $map;

    /**
     *  @param  [<string variantName> => <string presentationName>] $map
    **/
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        if (! $internal instanceof Internal) {
            throw new Exception(
                'Internal must be an instance of Internal',
                Exception::SELECT_EXPORT_INTERNAL_WRONG_TYPE
            );
        }

        $variantName = $internal->getName();
        if (! isset($this->map[$variantName])) {
            throw new Exception(
                'Unknown variant "' . $variantName . '"',
                Exception::SELECT_EXPORT_VARIANT_NAME_UNKNOWN
            );
        }
        return $internal->getValue()->to($this->map[$variantName]);
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        if (! $options instanceof Options) {
            throw new Exception(
                'Options must be an instance of Options, ' . get_class($options) . ' given',
                Exception::SELECT_IMPORT_OPTIONS_WRONG_TYPE
            );
        }
        foreach ($options as $variantName => $type) {
            if (isset($this->map[$variantName])) {
                try {
                    $value = $type->from($this->map[$variantName], $presentation);
                    if ($value) {
                        return new Internal($variantName, $value);
                    }
                } catch (PackageException $e) {}
            }
        }
    }
}
