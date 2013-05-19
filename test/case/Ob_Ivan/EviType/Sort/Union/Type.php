<?php

use Ob_Ivan\EviType\Sort\Enum\Builder  as EnumBuilder;
use Ob_Ivan\EviType\Sort\Union\Builder as UnionBuilder;
use Ob_Ivan\TestCase\AbstractCase;

class TypeTest extends AbstractCase
{
    public function setUp()
    {
        $this->enumBuilder  = new EnumBuilder;
        $this->unionBuilder = new UnionBuilder;
    }

    public function testEach()
    {
        $type = $this->unionBuilder->produce([[
            'alice' => $this->enumBuilder->produce([['a', 'l', 'i', 'c', 'e']]),
            'bob'   => $this->enumBuilder->produce([['B', 'o', 'b']]),
        ]]);

        foreach ($type->each() as $value) {
            $this->assertTrue($type->has($value), 'Value "' . $value . '" does not belong to type which spawned it');
        }
    }

    public function testRandom()
    {
        $type = $this->unionBuilder->produce([[
            'alice' => $this->enumBuilder->produce([['a', 'l', 'i', 'c', 'e']]),
            'bob'   => $this->enumBuilder->produce([['B', 'o', 'b']]),
        ]]);

        $value = $type->random();
        $this->assertTrue($type->has($value), 'Value does not belong to type which spawned it');
    }
}

