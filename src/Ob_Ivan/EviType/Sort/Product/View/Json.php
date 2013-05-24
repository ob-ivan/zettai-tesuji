<?php
namespace Ob_Ivan\EviType\Sort\Product\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;

class Json extends Associative
{
    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        return json_encode(parent::export($internal, $options), JSON_UNESCAPED_UNICODE);
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        return parent::import(json_decode($presentation, true), $options);
    }
}
