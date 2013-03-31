<?php
namespace Ob_Ivan\Compiler;

interface NodeFactoryInterface
{
    public function produce(
        $nodeType,
        $position,
        $length,
        NodeCollection $children,
        $value = null
    );
}
