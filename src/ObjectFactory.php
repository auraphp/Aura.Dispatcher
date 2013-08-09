<?php
namespace Aura\Invoker;

class ObjectFactory
{
    protected $map;
    
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }
    
    public function set($name, $callable)
    {
        $this->map[$name] = $callable;
    }
    
    public function get($name)
    {
        return $this->map[$name];
    }
    
    public function has($name)
    {
        return isset($this->map[$name]);
    }
    
    public function newInstance($spec)
    {
        $callable = $this->map[$spec];
        return $callable();
    }
}
