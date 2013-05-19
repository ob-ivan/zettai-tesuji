<?php
namespace Ob_Ivan\EviType\Sort\Product\Builder;

use Ob_Ivan\EviType\BuilderInterface;
use Ob_Ivan\EviType\Sort\Product\Internal,
    Ob_Ivan\EviType\Sort\Product\Options,
    Ob_Ivan\EviType\Sort\Product\Type;

class Record implements BuilderInterface
{
    /**
     * Строит тип-произведение из переданного массива.
     *
     *  @param  array   $arguments = [
     *      0 => [
     *          <componentName> => <TypeInterace type>,
     *          ...
     *      ]
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $options = new Options($arguments[0]);
        $type = new Type($options);
        $type->import('array', function ($presentation, Options $options) {
            foreach ($options as $componentName => $subType) {
                if (! isset($presentation[$componentName])) {
                    throw new Exception(
                        'Component "' . $componentName . '" is missing in presentation',
                        Exception::RECORD_IMPORT_ARRAY_COMPONENT_MISSING
                    );
                }
                if (! $subType->has($presentation[$componentName])) {
                    throw new Exception(
                        'Component "' . $componentName . '" does not belong to the expected type',
                        Exception::RECORD_IMPORT_ARRAY_COMPONENT_WRONG_TYPE
                    );
                }
            }
            return new Internal($presentation);
        });
        return $type;
    }
}
