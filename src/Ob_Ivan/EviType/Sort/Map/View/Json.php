<?php
/**
 * Представление отображения в виде JSON.
 *
 *  json([<domainValue->to(domainView)> => <rangeValue->to(rangeView)>])
**/
namespace Ob_Ivan\EviType\Sort\Map\View;

use Ob_Ivan\EviType\InternalInterface,
    Ob_Ivan\EviType\OptionsInterface;
use Ob_Ivan\EviType\Sort\Map\Internal;

class Json extends Pairs
{
    private $domainView;
    private $rangeView;

    public function __construct($domainView, $rangeView)
    {
        $this->domainView   = $domainView;
        $this->rangeView    = $rangeView;
    }

    public function export(InternalInterface $internal, OptionsInterface $options = null)
    {
        $map = [];
        foreach (parent::export($internal, $options) as $valuePair) {
            $map[$valuePair[0]->to($this->domainView)] = $valuePair[1]->to($this->rangeView);
        }
        return json_encode($map, JSON_UNESCAPED_UNICODE);
    }

    public function import($presentation, OptionsInterface $options = null)
    {
        $map = is_array($presentation) ? $presentation : json_decode($presentation, true);
        $valuePairs = [];
        $domain = $options->getDomain();
        $range  = $options->getRange();
        foreach ($map as $domainPresentation => $rangePresentation) {
            $domainValue = $domain->from($this->domainView, $domainPresentation);
            if (! $domainValue) {
                throw new Exception(
                    'Could not convert domain presentation "' . $domainPresentation . '" to domain value',
                    Exception::JSON_IMPORT_DOMAIN_PRESENTATION_NOT_RECOGNIZED
                );
            }
            $rangeValue = $range->from($this->rangeView, $rangePresentation);
            if (! $rangeValue) {
                throw new Exception(
                    'Could not convert range presentation "' . $rangePresentation . '" to range value',
                    Exception::JSON_IMPORT_RANGE_PRESENTATION_NOT_RECOGNIZED
                );
            }
            $valuePairs[] = [$domainValue, $rangeValue];
        }
        return new Internal($valuePairs);
    }
}
