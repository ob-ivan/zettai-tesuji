<?php
namespace Ob_Ivan\EviType;

interface TypeInterface
{
    public function __construct(OptionsInterface $options = null);

    // Создание и обслуживание значений //

    public function callValueMethod(InternalInterface $internal, $name, array $arguments);
    public function from($importName, $presentation);
    public function fromAny($presentation);
    public function has(Value $value);
    public function to($exportName, InternalInterface $internal);

    // Регистрация представлений //

    public function export($name, callable $implementation);
    public function import($name, callable $implementation);
    public function view($name, ViewInterface $view);
}
