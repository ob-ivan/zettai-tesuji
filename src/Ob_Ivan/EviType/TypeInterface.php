<?php
namespace Ob_Ivan\EviType;

interface TypeInterface
{
    // TODO: Ввести форму вызова from(viewName, presentation).
    public function from($presentation);

    public function to($viewName, $internal);
}
