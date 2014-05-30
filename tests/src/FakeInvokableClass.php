<?php
namespace Aura\Dispatcher;

class FakeInvokableClass
{
    public function __invoke($name = "World!")
    {
        return "Hello $name";
    }
}
