<?php
/**
 * Интерфейс для типов, которые умеют преобразовывать
 * свои значения в строки без указания на конкретный экспорт.
**/
namespace Ob_Ivan\EviType;

interface StringifierInterface
{
    public function stringify(InternalInterface $internal);
}
