<?php
namespace Zettai\AnswerCompiler;

use Ob_Ivan\Compiler\NodeCollection;
use Ob_Ivan\Compiler\NodeFactoryInterface;
use Ob_Ivan\Compiler\Token;

class NodeFactory implements NodeFactoryInterface
{
    public function produce($nodeType, Token $token = null, NodeCollection $children, $position, $length)
    {
        $className = __NAMESPACE__ . '\\Node\\' . $nodeType;
        return new $className($token, $children, $position, $length);
    }
}
