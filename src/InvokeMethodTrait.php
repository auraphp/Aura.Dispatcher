<?php
namespace Aura\Invoker;

use BadMethodCallException;
use Closure;
use ReflectionFunction;
use ReflectionMethod;

trait InvokeMethodTrait
{
    protected function invokeMethod(
        $object,
        $method = null,
        array $params = []
    ) {
        // is the object a closure?
        if ($object instanceof Closure) {
            // treat as a function; cf. https://bugs.php.net/bug.php?id=65432
            // ignore $method value
            $reflect = new ReflectionFunction($object);
        } else {
            // is the method callable?
            if (! is_callable([$object, $method])) {
                $message = get_class($object) . '::' . $method;
                throw new BadMethodCallException($message);
            }
            // treat as an object proper
            $reflect = new ReflectionMethod($object, $method);
        }
        
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
        if ($reflect instanceof ReflectionFunction) {
            return $reflect->invokeArgs($args);
        } else {
            return $reflect->invokeArgs($object, $args);
        }
    }
}
