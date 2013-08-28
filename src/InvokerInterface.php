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
 * Interface for invokers.
 * 
 * @package Aura.Invoker
 * 
 */
interface InvokerInterface
{
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
    public function __invoke(array $params = []);
    
    /**
     * 
     * Sets the parameter indicating the invokable object name.
     * 
     * @param string $object_param The parameter name to use.
     * 
     * @return null
     * 
     */
    public function setObjectParam($object_param);
    
    /**
     * 
     * Gets the parameter indicating the invokable object name.
     * 
     * @return string
     * 
     */
    public function getObjectParam();
    
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
    public function setObjects(array $objects);

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
    public function addObjects(array $objects);
    
    /**
     * 
     * Returns the array of invokable objects.
     * 
     * @return array
     * 
     */
    public function getObjects();
    
    /**
     * 
     * Sets an invokable object by name.
     * 
     * @param string $name The name.
     * 
     * @param object $object The invokable object.
     * 
     */
    public function setObject($name, $object);
    
    /**
     * 
     * Does an invokable object exist?
     * 
     * @param string $name The name of the invokable object.
     * 
     * @return bool
     * 
     */
    public function hasObject($name);
    
    /**
     * 
     * Returns an invokable object using its name.
     * 
     * @param string $name The name of the invokable object.
     * 
     * @return object
     * 
     */
    public function getObjectByName($name);
    
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
    public function getObjectByParams(array $params);
}
