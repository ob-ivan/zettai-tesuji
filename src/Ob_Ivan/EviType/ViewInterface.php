<?php
namespace Ob_Ivan\EviType;

interface ViewInterface
{
    public function export(InternalInterface $internal, OptionsInterface $options = null);

    public function import($presentation, OptionsInterface $options = null);
}
