<?php
namespace Zettai\Parameter;

use ArrayAccess;
use Silex\Controller;

class Service implements ArrayAccess
{
    // var //
    
    /**
     * @var [<ruleName> => [<methodName> => <methodArguments>]]
    **/
    private $rules = [];
    
    // public : ArrayAccess //
    
    public function offsetExists($offset)
    {
        return isset($this->rules[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->rules[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        // TODO: Проверить входное значение и привести к виду.
        $this->rules[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->rules[$offset]);
    }
    
    // public : Service //
    
    /**
     * Обвешивает контроллер правилами для своих параметров.
     *
     *  @param  Controller                      $controller
     *  @param  [<parameterName> => <ruleName>] $parameterRules
     *  @return Controller
    **/
    public function setParameters(Controller $controller, array $parameterRules)
    {
        foreach ($parameterRules as $parameterName => $ruleName) {
            foreach ($this->rules[$ruleName] as $methodName => $methodArguments) {
                if (! is_array($methodArguments)) {
                    $methodArguments = [$methodArguments];
                }
                array_unshift($methodArguments, $parameterName);
                call_user_func_array([$controller, $methodName], $methodArguments);
            }
        }
        return $controller;
    }
}
