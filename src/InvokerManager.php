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
 * Manager over object creation and invocations.
 * 
 * @package Aura.Invoker
 * 
 */
class InvokerManager
{
    use InvokerTrait;
    
    /**
     * 
     * The parameter name indicating what object to create from the factory.
     * 
     * @var string
     * 
     */
    protected $object_param;
    
    /**
     * 
     * The parameter name indicating what method to invoke on the object.
     * 
     * @var string
     * 
     */
    protected $method_param;
    
    /**
     * 
     * The object to work with.
     * 
     * @var object
     * 
     */
    protected $object;
    
    /**
     * 
     * The method to invoke on the object.
     * 
     * @var string
     * 
     */
    protected $method;
    
    /**
     * 
     * The params for the invocation.
     * 
     * @var array
     * 
     */
    protected $params;
    
    /**
     * 
     * Constructor.
     * 
     * @param ObjectFactory $object_factory A factory for creating objects to
     * work with.
     * 
     * @param string $object_param The parameter name indicating what object
     * to create from the factory.
     * 
     * @param string $method The parameter name indicating what method to call
     * on the object.
     * 
     */
    public function __construct(
        array $factories = [],
        $object_param = null,
        $method_param = null
    ) {
        $this->setFactories($factories);
        $this->setObjectParam($object_param);
        $this->setMethodParam($method_param);
    }
    
    /**
     * 
     * Set the factories that create named objects; this clears out all
     * previous factories.
     * 
     * @param array $factories An array where the key is an object name and
     * the value is a callable that returns a new instance of that object.
     * 
     * @return null
     * 
     */
    public function setFactories(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * 
     * Adds to the factories that create named objects; this merges with the
     * previous factories.
     * 
     * @param array $factories An array where the key is an object name and
     * the value is a callable that returns a new instance of that object.
     * 
     * @return null
     * 
     */
    public function addFactories(array $factories)
    {
        $this->factories = array_merge($this->factories, $factories);
    }
    
    /**
     * 
     * Returns the array of named object factories.
     * 
     * @return array
     * 
     */
    public function getFactories()
    {
        return $this->factories;
    }
    
    /**
     * 
     * Sets the factory for one named object.
     * 
     * @param string $name The object name.
     * 
     * @param callable $factory A callable that returns a new instance of the
     * object.
     * 
     */
    public function setFactory($name, $factory)
    {
        $this->factories[$name] = $factory;
    }
    
    /**
     * 
     * Does a factory exist for a named object?
     * 
     * @param string $name The named object.
     * 
     * @return bool
     * 
     */
    public function hasFactory($name)
    {
        return isset($this->factories[$name]);
    }
    
    /**
     * 
     * Returns a single factory by name.
     * 
     * @param string $name The object factory name.
     * 
     * @return callable
     * 
     */
    public function getFactory($name)
    {
        if ($this->hasFactory($name)) {
            return $this->factories[$name];
        }
        
        throw new Exception\FactoryNotDefined($name);
    }
    
    /**
     * 
     * Returns a new instance of the named object using its factory callable.
     * 
     * @param string $name The object name.
     * 
     * @return object A new instance of the named object.
     * 
     */
    public function newInstance($name)
    {
        $factory = $this->getFactory($name);
        return $factory();
    }
    
    /**
     * 
     * Sets the parameter name indicating what object to create from the
     * factories.
     * 
     * @param string $object_param The parameter name to use.
     * 
     * @return null
     * 
     */
    public function setObjectParam($object_param)
    {
        $this->object_param = $object_param;
    }
    
    /**
     * 
     * Gets the parameter name indicating what object to create from the
     * factories.
     * 
     * @return string
     * 
     */
    public function getObjectParam()
    {
        return $this->object_param;
    }
    
    /**
     * 
     * Sets the parameter name indicating what method to call on the object.
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
     * Gets the parameter name indicating what method to call on the object.
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
     * Given a set of parameters, creates an object and invokes a method on
     * it with the remaining named parameters.
     * 
     * @param array $params The parameters to use for invoking the object
     * method; these may contain a param indicating what object to create and
     * what method to call on it.
     * 
     * @param object $object An explicit object to use instead of creating it
     * from the factories; this overrides any param indicating what object to
     * create from the factories.
     * 
     * @param string $method An explicit method to call on the object instead
     * of picking it from the params; if the object is a Closure, this value
     * will be ignored.
     * 
     * @return mixed The return from the invoked object method.
     * 
     */
    public function exec(array $params = [], $object = null, $method = null)
    {
        // set these properties in order
        $this->setParams($params);
        $this->setObject($object);
        $this->setMethod($method);
        
        // if the object is already a closure, invoke it directly
        if ($this->object instanceof Closure) {
            return $this->invokeClosure($this->object, $this->params);
        }
        
        // otherwise, invoke a method on the object with named params
        return $this->invokeMethod(
            $this->object,
            $this->method,
            $this->params
        );
    }
    
    /**
     * 
     * Makes sure the the $params property is set properly.
     * 
     * @return null
     * 
     */
    protected function setParams(array $params)
    {
        // set the property
        $this->params = $params;
    }
    
    /**
     * 
     * Makes sure the the $object property is set properly.
     * 
     * @return null
     * 
     */
    protected function setObject($object)
    {
        // set the property
        $this->object = $object;
        
        // are we missing the object spec?
        if (! $this->object) {
            // no, set it from the params
            $this->object = isset($this->params[$this->object_param])
                          ? $this->params[$this->object_param]
                          : null;
        }
        
        // are we still missing the object spec?
        if (! $this->object) {
            throw new Exception\ObjectNotSpecified;
        }
        
        // is it actually an object?
        if (! is_object($this->object)) {
            // treat it as a spec for the factory
            $this->object = $this->newInstance($this->object);
        }
    }
    
    /**
     * 
     * Makes sure the the $method property is set properly.
     * 
     * @return null
     * 
     */
    protected function setMethod($method)
    {
        // set the property
        $this->method = $method;
        
        // if the object is a closure, no method is called
        if ($this->object instanceof Closure) {
            $this->method = null;
            return;
        }
        
        // are we missing the method?
        if (! $this->method) {
            // set it from the params
            $this->method = isset($this->params[$this->method_param])
                          ? $this->params[$this->method_param]
                          : null;
        }
        
        // are we still missing the method?
        if (! $this->method) {
            throw new Exception\MethodNotSpecified;
        }
    }
}
