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

/**
 * 
 * Uses each invokable object as a factory; invokes the factory to create an
 * object, then invokes a method on the created object using named parameters.
 * 
 * @package Aura.Invoker
 * 
 */
class FactoryInvoker extends AbstractInvoker
{
    use InvokeMethodTrait;
    
    /**
     * 
     * The parameter indicating the method to invoke on the created object.
     * 
     * @var string
     * 
     */
    protected $method_param;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $objects An array of invokable objects keyed by name.
     * 
     * @param string $object_param The param indicating the invokable object
     * name.
     * 
     * @param string $method_param The paramindicating what method to call
     * on the factoried object.
     * 
     */
    public function __construct(
        array $objects = [],
        $object_param = null,
        $method_param = null
    ) {
        parent::__construct($objects, $object_param);
        $this->setMethodParam($method_param);
    }
    
    /**
     * 
     * Uses the params to get an invokable object as a factory, creates an
     * object using that factory, and invokes a method on the created object.
     * 
     * @param array $params Named params for the invocation.
     * 
     * @return mixed The return from the method on the created object.
     * 
     */
    public function __invoke(array $params = [])
    {
        $object = $this->getObjectByParams($params);
        $method = $this->getMethodByParams($params);
        return $this->invokeMethod($object, $method, $params);
    }
    
    /**
     * 
     * Gets the method from the params.
     * 
     * @return null
     * 
     */
    protected function getMethodByParams($params)
    {
        $key = $this->method_param;
        if (isset($params[$key])) {
            return $params[$key];
        }
        
        throw new Exception\MethodNotSpecified;
    }
    
    /**
     * 
     * Sets the parameter indicating the method to call on the created object.
     * 
     * @param string $method_param The parameter name to use.
     * 
     * @return null
     * 
     */
    public function setMethodParam($method_param)
    {
        $this->method_param = $method_param;
    }
    
    /**
     * 
     * Gets the parameter indicating the method to call on the created object.
     * 
     * @return string
     * 
     */
    public function getMethodParam()
    {
        return $this->method_param;
    }
    
    /**
     * 
     * Looks up an invokable object by name, then uses it as a factory to
     * create an object, and returns the created object.
     * 
     * @param string $name The name of the invokable object.
     * 
     * @return object The object created by the invokable object.
     * 
     */
    public function getObjectByName($name)
    {
        $object = parent::getObjectByName($name);
        return $object();
    }
    
    /**
     * 
     * Looks up an invokable object using an array of params; if the
     * `$object_param` is an object, it is returned directly, otherwise it is
     * treated as an invokable object name; the invokable object name is used
     * as a factory to create an object, then returns the created object.
     * 
     * @param array $params Params to look up the invokable object.
     * 
     * @return object The object created by using invokable object as a
     * factory.
     * 
     */
    public function getObjectByParams(array $params)
    {
        $object = parent::getObjectByParams($params);
        return $object();
    }
}
