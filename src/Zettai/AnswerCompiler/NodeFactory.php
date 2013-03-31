<?php
namespace Zettai\AnswerCompiler;

use Ob_Ivan\Compiler\NodeCollection;
use Ob_Ivan\Compiler\NodeFactoryInterface;

class NodeFactory implements NodeFactoryInterface
{
    public function produce(
        $nodeType,
        $position,
        $length,
        NodeCollection $children,
        $value = null
    ) {
        $className = __NAMESPACE__ . '\\Node\\' . $nodeType;
        return new $className($position, $length, $children, $value);
    }
}
