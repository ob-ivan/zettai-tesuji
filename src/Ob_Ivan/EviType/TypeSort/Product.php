<?php
namespace Ob_Ivan\EviType\TypeSort;

use Ob_Ivan\EviType\Type;
use Ob_Ivan\EviType\TypeSortInterface;
use Ob_Ivan\EviType\ViewFactory;
use Ob_Ivan\EviType\ViewService;
use Ob_Ivan\EviType\ViewSort\Product\Concat;
use Ob_Ivan\EviType\ViewSort\Product\Separator;

class Product implements TypeSortInterface
{
    /**
     * @var ViewFactory;
    **/
    private $factory;

    public function __construct()
    {
        $this->factory = new ViewFactory();

        $this->factory->register([
            'concat'    => function () { return new Concat();    },
            'separator' => function () { return new Separator(); },
        ]);
    }

    public function call(Type $type, $method, array $arguments)
    {
        // TODO
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
