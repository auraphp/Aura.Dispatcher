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
use ReflectionFunction;

/**
 * 
 * Invokes a closure with named parameters.
 * 
 * @package Aura.Dispatcher
 * 
 */
trait InvokeClosureTrait
{
    /**
     * 
     * Invokes a closure with named parameters.
     * 
     * @param Closure $closure The closure to work with.
     * 
     * @param array $params An array of key-value pairs to use as params for
     * the method; the array keys are matched to the closure param names.
     * 
     * @return mixed The return of the invoked closure.
     * 
     */
    protected function invokeClosure(Closure $closure, array $params = [])
    {
        // treat as a function; cf. https://bugs.php.net/bug.php?id=65432
        $reflect = new ReflectionFunction($closure);
        
        // sequential arguments when invoking
        $args = [];
        
        // match named params with arguments
        foreach ($reflect->getParameters() as $i => $param) {
            if (isset($params[$param->name])) {
                // a named param value is available
                $args[] = $params[$param->name];
            } elseif (isset($params[$i])) {
                // a positional param value is available
                $args[] = $params[$i];
            } else {
                // use the default value, or null if there is none
                $args[] = $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
            }
        }
        
        // invoke with the args, and done
        return $reflect->invokeArgs($args);
    }
}
