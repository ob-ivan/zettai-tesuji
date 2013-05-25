<?php
namespace Ob_Ivan\EviType;

use Ob_Ivan\EviType\Sort\IterableInterface;

abstract class Type implements TypeInterface
{
    // var //

    /**
     *  @var [<string name> => <mixed implementation(Internal internal, Options options)>]
    **/
    private $exports = [];

    /**
     *  @var [<string name> => <mixed implementation(Internal internal, Options options)>]
    **/
    private $getters = [];

    /**
     *  @var [<string name> => <Internal implementation(mixed presentation, Options options)>]
    **/
    private $imports = [];

    /**
     *  @var [<string name> => <ViewInterface view>]
    **/
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

    public function exists($getterName)
    {
        $name = $this->normalizeName($getterName);
        return isset($this->getters[$name]);
    }

    public function from($importName, $presentation)
    {
        $name = $this->normalizeName($importName);
        if (isset($this->imports[$name])) {
            $internal = $this->imports[$name]($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        } elseif (isset($this->views[$name])) {
            $internal = $this->views[$name]->import($presentation, $this->options);
            if ($internal) {
                return $this->valueService->produce($internal);
            }
        }
        throw new Exception(
            'Could not import "' . $presentation . '" as "' . $importName . '" in class ' . get_called_class(),
            Exception::TYPE_FROM_IMPORT_FAIL
        );
    }

    public function fromAny($presentation)
    {
        if ($this->has($presentation)) {
            return $presentation;
        }
        foreach ($this->imports as $import) {
            try {
                $internal = $import($presentation, $this->options);
                if ($internal) {
                    return $this->valueService->produce($internal);
                }
            } catch (Exception $e) {}
        }
        foreach ($this->views as $view) {
            try {
                $internal = $view->import($presentation, $this->options);
                if ($internal) {
                    return $this->valueService->produce($internal);
                }
            } catch (Exception $e) {}
        }
        throw new Exception(
            'Could not import "' . $presentation . '" as any in class ' . get_called_class(),
            Exception::TYPE_FROM_ANY_FAIL
        );
    }

    public function get($getterName, InternalInterface $internal)
    {
        $name = $this->normalizeName($getterName);
        if (isset($this->getters[$name])) {
            return $this->getters[$name]($internal, $this->options);
        }
    }

    public function has($value)
    {
        if (! $value instanceof Value) {
            return false;
        }
        return $value->belongsTo($this);
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
        throw new Exception(
            'Unknown export name "' . $exportName . '" in class ' . get_called_class(),
            Exception::TYPE_TO_EXPORT_NAME_UNKNOWN
        );
    }

    // public : TypeInterface : Регистрация представлений //

    public function export($name, callable $implementation)
    {
        $this->exports[$this->normalizeName($name)] = $implementation;
        return $this;
    }

    public function getter($name, callable $implementation)
    {
        $this->getters[$this->normalizeName($name)] = $implementation;
        return $this;
    }

    public function import($name, callable $implementation)
    {
        $this->imports[$this->normalizeName($name)] = $implementation;
        return $this;
    }

    public function view($name, ViewInterface $view)
    {
        $this->views[$this->normalizeName($name)] = $view;
        return $this;
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

    public function random()
    {
        if (! $this instanceof IterableInterface) {
            throw new Exception(
                'Random is not supported for this type',
                Exception::TYPE_RANDOM_INTERFACE_NOT_IMPLEMENTED
            );
        }
        $each = $this->each();
        return $each[array_rand($each)];
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
