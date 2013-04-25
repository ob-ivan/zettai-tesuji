<?php
/**
 * Представление, сопоставляющее каждому варианту своё подпредставление.
**/
namespace Ob_Ivan\EviType\Type\Union\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface,
    Ob_Ivan\EviType\ViewInterface;

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
        $variantName = $internal->getName();
        if (! isset($this->map[$variantName])) {
            throw new Exception(
                'Unknown variant "' . $variantName . '"',
                Exception::SELECT_EXPORT_VARIANT_NAME_UNKNOWN
            );
        }
        $exportName = $this->map[$variantName];
        return $internal->to($exportName);
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        foreach ($options as $variantName => $type) {
            if (isset($this->map[$variantName])) {
                $value = $type->from($this->map[$variantName], $presentation);
                if ($value) {
                    return new Internal($variantName, $value);
                }
            }
        }
    }
}
