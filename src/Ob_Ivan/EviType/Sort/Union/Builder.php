<?php
namespace Ob_Ivan\EviType\Sort\Union;

use Ob_Ivan\EviType\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * Строит тип-объединение из переданного массива.
     *
     *  @param  array   $arguments = [
     *      0 => [
     *          <variantName> => <TypeInterace type>,
     *          ...
     *      ]
     *  ]
     *  @return Type
    **/
    public function produce(array $arguments = null)
    {
        $options = new Options($arguments[0]);
        $type = new Type($options);
        // TODO: Добавить стандартные экспорты:
        //  - Получить название текущего варианта.
        //  - Получить значение текущего варианта.
        // Стандартный импорт:
        //  - Скастовать значение варианта в значение объединения.
        return $type;
    }
}
