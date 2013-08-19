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
        ObjectFactory $object_factory,
        $object_param = null,
        $method_param = null
    ) {
        $this->object_factory = $object_factory;
        $this->setObjectParam($object_param);
        $this->setMethodParam($method_param);
    }
    
    /**
     * 
     * Sets the arameter name indicating what object to create from the
     * factory.
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
     * Sets the arameter name indicating what method to call on the object.
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
     * Returns the object factory.
     * 
     * @return ObjectFactory
     * 
     */
    public function getObjectFactory()
    {
        return $this->object_factory;
    }
    
    /**
     * 
     * Given a set of parameters, creates an object and invokes a method on
     * it with the remaining named parameters.
     * 
     * @param array $params The parameters to use for creating the object from
     * the factory and the method to call on it.
     * 
     * @param object $object An explicit object to use instead of creating it
     * from the factory.
     * 
     * @param string $method An explicit method to call on the object instead
     * of picking it from the params.
     * 
     * @return mixed The return from the invoked object method.
     * 
     */
    public function exec(array $params = [], $object = null, $method = null)
    {
        $this->params = $params;
        $this->object = $object;
        $this->method = $method;
        
        $this->fixObject();
        $this->fixMethod();
        
        if ($this->object instanceof Closure) {
            return $this->invokeClosure($this->object, $this->params);
        }
        
        return $this->invokeMethod(
            $this->object,
            $this->method,
            $this->params
        );
    }
    
    /**
     * 
     * Makes sure the the $object property is set properly.
     * 
     * @return null
     * 
     */
    protected function fixObject()
    {
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
            $this->object = $this->object_factory->newInstance($this->object);
        }
    }
    
    /**
     * 
     * Makes sure the the $method property is set properly.
     * 
     * @return null
     * 
     */
    protected function fixMethod()
    {
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
