<?php
namespace Ob_Ivan\EviType;

interface TypeInterface
{
    public function __construct(TypeOptionsInterface $options = null);

    public function callValueMethod(InternalInterface $internal, $name, array $arguments);

    // Регистрация представлений.

    public function export($name, callable $implementation);
    public function import($name, callable $implementation);
    public function view($name, ViewInterface $view);
}
