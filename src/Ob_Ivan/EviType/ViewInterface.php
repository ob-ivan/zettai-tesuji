<?php
namespace Ob_Ivan\EviType;

interface ViewInterface
{
    public function export(InternalInterface $internal, TypeOptionsInterface $options = null);

    public function import($presentation, ValueService $valueService);
}
