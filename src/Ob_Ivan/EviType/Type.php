<?php
namespace Ob_Ivan\EviType;

abstract class Type implements TypeInterface
{
    // var //

    private $exports = [];
    private $imports = [];
    private $views   = [];

    /**
     *  @var OptionsInterface
    **/
    private $options;

    /**
     *  @var ValueService
    **/
    private $valueService;

    // public : TypeInterface //

    public function __construct(OptionsInterface $options = null)
    {
        $this->options = $options;

        $this->valueService = new ValueService($this);
    }

    public function export($name, callable $implementation)
    {
        $this->exports[$name] = $implementation;
    }

    public function import($name, callable $implementation)
    {
        $this->imports[$name] = $implementation;
    }

    public function view($name, ViewInterface $view)
    {
        $this->views[$name] = $view;
    }
}
