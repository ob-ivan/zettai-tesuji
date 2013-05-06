<?php
/**
 * Интерфейс для типов значений, у которых можно получать отдельные координаты.
**/
namespace Ob_Ivan\EviType\Type;

use Ob_Ivan\EviType\InternalInterface;

interface DereferencerInterface
{
    public function dereferenceExists(InternalInterface $internal, $offset);
    public function dereferenceGet   (InternalInterface $internal, $offset);
}
