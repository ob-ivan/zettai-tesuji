<?php
namespace Zettai\Type;

use Closure;

trait HookTrait
{
    // var //
    
    /**
     *  @var [<string hookName> => <callable Hook>]
    **/
    private $hooks = [];
    
    // public //
    
    public function setHook($hookName, callable $hook)
    {
        $this->hooks[$hookName] = $hook;
        return $this;
    }
    
    // protected //
    
    protected function callHook($hookName, $args)
    {
        return call_user_func_array(Closure::bind($this->getHook($hookName), $this, $this), $args);
    }
    
    protected function getHook($hookName)
    {
        if (! $this->hasHook($hookName)) {
            return null;
        }
        return $this->hooks[$hookName];
    }

    protected function hasHook($hookName)
    {
        return isset($this->hooks[$hookName]);
    }
}
