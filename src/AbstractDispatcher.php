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

/**
 * 
 * Base class for other dispatchers.
 * 
 * @package Aura.Dispatcher
 * 
 */
abstract class AbstractDispatcher implements DispatcherInterface
{
    /**
     * 
     * Invokable objects.
     * 
     * @var array
     * 
     */
    protected $objects = [];
    
    /**
     * 
     * The param indicating the invokable object name.
     * 
     * @var string
     * 
     */
    protected $object_param;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $objects An array of invokable objects keyed by name.
     * 
     * @param string $object_param The param indicating the invokable object
     * name.
     * 
     */
    public function __construct(array $objects = [], $object_param = null)
    {
        $this->setObjects($objects);
        $this->setObjectParam($object_param);
    }
    
    /**
     * 
     * Uses the params to get an invokable object, then invokes it with the
     * same params.
     * 
     * @param array $params Named params for the invocation.
     * 
     * @return mixed The return from the invoked object.
     * 
     */
    abstract public function __invoke(array $params = []);
    
    /**
     * 
     * Sets the parameter indicating the invokable object name.
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
     * Gets the parameter indicating the invokable object name.
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
     * Set the array of invokable objects; this clears all existing objects.
     * 
     * @param array $objects An array where the key is a name and the value
     * is an invokable object.
     * 
     * @return null
     * 
     */
    public function setObjects(array $objects)
    {
        $this->objects = $objects;
    }

    /**
     * 
     * Adds to the array of invokable objects; this merges with existing
     * objects.
     * 
     * @param array $objects An array where the key is a name and the value
     * is an invokable object.
     * 
     * @return null
     * 
     */
    public function addObjects(array $objects)
    {
        $this->objects = array_merge($this->objects, $objects);
    }
    
    /**
     * 
     * Returns the array of invokable objects.
     * 
     * @return array
     * 
     */
    public function getObjects()
    {
        return $this->objects;
    }
    
    /**
     * 
     * Sets an invokable object by name.
     * 
     * @param string $name The name.
     * 
     * @param object $object The invokable object.
     * 
     */
    public function setObject($name, $object)
    {
        $this->objects[$name] = $object;
    }
    
    /**
     * 
     * Does an invokable object exist?
     * 
     * @param string $name The name of the invokable object.
     * 
     * @return bool
     * 
     */
    public function hasObject($name)
    {
        return isset($this->objects[$name]);
    }
    
    /**
     * 
     * Returns an invokable object using its name.
     * 
     * @param string $name The name of the invokable object.
     * 
     * @return object
     * 
     */
    public function getObjectByName($name)
    {
        if ($this->hasObject($name)) {
            return $this->objects[$name];
        }
        
        throw new Exception\ObjectNotDefined($name);
    }
    
    /**
     * 
     * Returns an invokable object using an array of params; if the
     * `$object_param` is an object, it is returned directly, otherwise it is
     * treated as an invokable object name.
     * 
     * @param array $params Params to look up the invokable object.
     * 
     * @return object The invokable object.
     * 
     */
    public function getObjectByParams(array $params)
    {
        // do we have an object param in the params?
        $key = $this->getObjectParam();
        if (! isset($params[$key])) {
            throw new Exception\ObjectNotSpecified;
        }
        
        // is the object param value already an object?
        $value = $params[$key];
        if (is_object($value)) {
            return $value;
        }
        
        // get the invokable object by name
        if ($this->hasObject($value)) {
            return $this->objects[$value];
        }
        
        // could not find the invokable object by name
        throw new Exception\ObjectNotDefined($value);
    }
}
