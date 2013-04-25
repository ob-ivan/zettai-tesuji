<?php
namespace Ob_Ivan\EviType;

abstract class Type implements TypeInterface
{
    // var //

    /**
     *  @var [<string name> => <mixed implementation(Internal internal, Options options)>]
    **/
    private $exports = [];

    /**
     *  @var [<string name> => <Internal implementation(mixed presentation, Options options)>]
    **/
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

    // public : TypeInterface : Создание и обслуживание значений //

    public function from($importName, $presentation)
    {
        if (isset($this->imports[$importName])) {
            $internal = $this->imports[$importName]($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
        if (isset($this->views[$importName])) {
            $internal = $this->views[$importName]->import($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
    }

    public function fromAny($presentation)
    {
        foreach ($this->imports as $import) {
            $internal = $import($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
        foreach ($this->views as $view) {
            $internal = $view->import($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
    }

    // public : TypeInterface : Регистрация представлений //

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
