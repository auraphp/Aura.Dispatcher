<?php
namespace Aura\Invoker;

class MockBase
{
    use InvokerTrait;
    
    public function exec($method, array $params = [])
    {
        return $this->invokeMethod($this, $method, $params);
    }
    
    public function publicMethod($foo, $bar, $baz = 'baz')
    {
        return "$foo $bar $baz";
    }
    
    protected function protectedMethod($foo, $bar, $baz = 'baz')
    {
        return "$foo $bar $baz";
    }
    
    private function privateMethod($foo, $bar, $baz = 'baz')
    {
        return "$foo $bar $baz";
    }    
}
