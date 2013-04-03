<?php
namespace Ob_Ivan\EviType\TypeSort;

use Ob_Ivan\EviType\Type;
use Ob_Ivan\EviType\TypeSortInterface;
use Ob_Ivan\EviType\ViewFactory;
use Ob_Ivan\EviType\ViewService;
use Ob_Ivan\EviType\ViewSort;

class Enum implements TypeSortInterface
{
    /**
     * @var ViewFactory;
    **/
    private $factory;

    public function __construct()
    {
        $this->factory = new ViewFactory();

        $this->factory->register([
            'dictionary' => function () { new ViewSort\Dictionary(); },
        ]);
    }

    public function call(Type $type, $method, array $arguments)
    {
        switch ($method) {
            case 'each':
                // TODO
                break;
        }
    }

    public function produce(array $arguments)
    {
        return new Type($this, $arguments);
    }

    public function view(Type $type)
    {
        return new ViewService($this->factory);
    }
}
