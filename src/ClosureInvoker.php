<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Invoker
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Invoker;

use Closure;

/**
 * 
 * Uses each invokable object as a closure; invokes the closure using named
 * parameters.
 * 
 * @package Aura.Invoker
 * 
 */
class ClosureInvoker extends AbstractInvoker
{
    use InvokeClosureTrait;
    
    /**
     * 
     * Uses the params to get a closure object, then invokes the closure with
     * the same params.
     * 
     * @param array $params Named params for the invocation.
     * 
     * @return mixed The return from the invoked closure.
     * 
     */
    public function __invoke(array $params = [])
    {
        $object = $this->getObjectByParams($params);
        return $this->invokeClosure($object, $params);
    }
}
