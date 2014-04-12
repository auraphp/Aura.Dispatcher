<?php
namespace Aura\Dispatcher;

class FakeInvokable extends FakeBase
{
    public function __invoke($foo, $bar, $baz = 'baz')
    {
        return "$foo $bar $baz";
    }
}
