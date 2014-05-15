<?php
namespace Aura\Dispatcher;

class FakeClassWithMethod
{
    public function someAction()
    {
        return new FakeInvokableClass();
    }
}
