<?php
namespace Ob_Ivan\EviType;

interface ViewInterface
{
    public function export(InternalInterface $internal);

    public function import($presentation);
}
