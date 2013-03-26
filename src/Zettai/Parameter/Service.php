<?php
namespace Zettai\Parameter;

use ArrayAccess;
use Silex\Controller;

class Service implements ArrayAccess
{
    // var //
    
    /**
     * @var [<parameterName> => [<methodName> => <methodArguments>]]
    **/
    private $options = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->options[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        // TODO: Проверить входное значение и привести к виду.
        $this->options[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->options[$offset]);
    }
    
    // public : Service //
    
    public function setParameters(Controller $controller, array $parameterNames)
    {
        foreach ($parameterNames as $parameterName) {
            foreach ($this->options[$parameterName] as $methodName => $methodArguments) {
                if (! is_array($methodArguments)) {
                    $methodArguments = [$methodArguments];
                }
                array_unshift($methodArguments, $parameterName);
                call_user_func_array([$controller, $methodName], $methodArguments);
            }
        }
    }
}
