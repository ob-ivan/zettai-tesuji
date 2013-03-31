<?php
namespace Ob_Ivan\Compiler;

interface NodeFactoryInterface
{
    public function produce($nodeType, Token $token = null, NodeCollection $children, $position, $length);
}
