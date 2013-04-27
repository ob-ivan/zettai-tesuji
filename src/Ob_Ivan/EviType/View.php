<?php
namespace Ob_Ivan\EviType;

class View implements ViewInterface
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

    public function from($presentation)
    {
        return $this->sort->from($this->arguments, $presentation);
    }

    public function to($internal)
    {
        return $this->sort->to($this->arguments, $internal);
    }
}
