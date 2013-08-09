<?php
namespace Aura\Invoker;

class FakeObject
{
    public function foobarbaz($foo, $bar, $baz = 'baz')
    {
        return "$foo $bar $baz";
    }
}
