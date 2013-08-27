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

use ReflectionMethod;

/**
 * 
 * Invokes an object method with named parameters, honoring method scope
 * relative to `$this`.
 * 
 * @package Aura.Invoker
 * 
 */
trait InvokeMethodTrait
{
    /**
     * 
     * Invokes an object method with named parameters, honoring method scope
     * relative to `$this`.
     * 
     * @param object $object The object to work with.
     * 
     * @param string $method The method to invoke on the object.
     * 
     * @param array $params An array of key-value pairs to use as params for
     * the method; the array keys are matched to the method param names.
     * 
     * @return mixed The return of the invoked object method.
     * 
     */
    protected function invokeMethod($object, $method, array $params = [])
    {
        // is the method defined?
        if (! method_exists($object, $method)) {
            $message = get_class($object) . '::' . $method;
            throw new Exception\MethodNotDefined($message);
        }
        
        // reflect on the object method
        $reflect = new ReflectionMethod($object, $method);
        
        // check accessibility from $this to honor protected/private methods
        $accessible = true;
        if ($reflect->isProtected()) {
            $access = 'protected';
            $accessible = ($object instanceof $this);
        } elseif ($reflect->isPrivate()) {
            $access = 'private';
            $declaring_class = $reflect->getDeclaringClass()->getName();
            $accessible = ($declaring_class == get_class($this));
        }
        
        // is the method accessible by $this?
        if (! $accessible) {
            $message = get_class($object) . "::$method is $access";
            throw new Exception\MethodNotAccessible($message);
        }
        
        // the method is accessible by $this
        $reflect->setAccessible(true);
        
        // sequential arguments when invoking
        $args = [];
        
        // match named params with arguments
        foreach ($reflect->getParameters() as $param) {
            if (isset($params[$param->name])) {
                // a named param value is available
                $args[] = $params[$param->name];
            } else {
                // use the default value, or null if there is none
                $args[] = $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
            }
        }
        
        // invoke with the args, and done
        return $reflect->invokeArgs($object, $args);
    }
}
