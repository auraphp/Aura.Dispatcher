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
 * A factory to create invokable objects.
 * 
 * @package Aura.Invoker
 * 
 */
class ObjectFactory
{
    /**
     * 
     * A map of object names to callables that create objects.
     * 
     * @param array
     * 
     */
    protected $map = [];
    
    /**
     * 
     * Constructor.
     * 
     * @param array $map A map of object names to callables that create
     * objects.
     * 
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }
    
    /**
     * 
     * Maps an object name to a callable that creates that object.
     * 
     * @param string $name The object name.
     * 
     * @param callable $callable The callable to create the object.
     * 
     * @return null
     * 
     */
    public function set($name, $callable)
    {
        $this->map[$name] = $callable;
    }
    
    /**
     * 
     * Gets the callable that creates a named object.
     * 
     * @param string $name The object name.
     * 
     * @return callable $callable The callable to create the object.
     * 
     */
    public function get($name)
    {
        return $this->map[$name];
    }
    
    /**
     * 
     * Does the named object exist in the map?
     * 
     * @param string $name The object name.
     * 
     * @return bool
     * 
     */
    public function has($name)
    {
        return isset($this->map[$name]);
    }
    
    /**
     * 
     * Returns a new instance of the named object.
     * 
     * @param string $name The object name.
     * 
     * @return object
     * 
     */
    public function newInstance($name)
    {
        $callable = $this->map[$name];
        return $callable();
    }
}
