<?php
namespace Ob_Ivan\EviType;

class View
{
    private $arguments;

    /**
     * @var ViewSort
    **/
    private $sort;

    public function __construct(ViewSortInterface $sort, array $arguments)
    {
        $this->sort      = $sort;
        $this->arguments = $arguments;
    }
}
