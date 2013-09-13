<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Dispatcher
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Dispatcher;

use Closure;

/**
 * 
 * Uses each invokable object as a closure; invokes the closure using named
 * parameters.
 * 
 * @package Aura.Dispatcher
 * 
 */
class ClosureDispatcher extends AbstractDispatcher
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
