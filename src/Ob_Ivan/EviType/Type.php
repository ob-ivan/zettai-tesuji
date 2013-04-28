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

    public function callValueMethod(InternalInterface $internal, $name, array $arguments)
    {
        if ($name === 'to') {
            return $this->to($arguments[0], $internal);
        }
        if (preg_match('~^to(\w+)$~', $name, $matches)) {
            return $this->to($matches[1], $internal);
        }
        throw new Exception(
            'Unknown method "' . $name . '" in class ' . get_called_class(),
            Exception::TYPE_CALL_VALUE_METHOD_NAME_UNKNOWN
        );
    }

    public function from($importName, $presentation)
    {
        $name = $this->normalizeName($importName);
        if (isset($this->imports[$name])) {
            $internal = $this->imports[$name]($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
        if (isset($this->views[$name])) {
            $internal = $this->views[$name]->import($presentation, $this->options);
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

    public function to($exportName, InternalInterface $internal)
    {
        $name = $this->normalizeName($exportName);
        if (isset($this->exports[$name])) {
            return $this->exports[$name]($internal, $this->options);
        }
        if (isset($this->views[$name])) {
            return $this->views[$name]->export($internal, $this->options);
        }
    }

    // public : TypeInterface : Регистрация представлений //

    public function export($name, callable $implementation)
    {
        $this->exports[$this->normalizeName($name)] = $implementation;
    }

    public function import($name, callable $implementation)
    {
        $this->imports[$this->normalizeName($name)] = $implementation;
    }

    public function view($name, ViewInterface $view)
    {
        $this->views[$this->normalizeName($name)] = $view;
    }

    // public : Type //

    public function __call($name, $arguments)
    {
        if (preg_match('~^from(\w+)$~', $name, $matches)) {
            return $this->from($matches[1], $arguments[0]);
        }
        throw new Exception(
            'Unknown method "' . $name . '" in class ' . get_called_class(),
            Exception::TYPE_CALL_NAME_UNKNOWN
        );
    }

    // protected //

    protected function getOptions()
    {
        return $this->options;
    }

    protected function produceValue(InternalInterface $internal)
    {
        return $this->valueService->produce($internal);
    }

    // private //

    /**
     * Приводит название преобразователей к единому виду.
     *
     *  @param  string  $name
     *  @return string
    **/
    private function normalizeName($name)
    {
        return strtolower($name);
    }
}
