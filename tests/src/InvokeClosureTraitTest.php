<?php
namespace Aura\Invoker;

class InvokeClosureTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokeClosureTrait;
    
    public function testInvokeClosure()
    {
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        $expect = 'FOO BAR baz';
        $actual = $this->invokeClosure($closure, [
            'foo' => 'FOO',
            'bar' => 'BAR',
        ]);
        $this->assertSame($expect, $actual);
    }
}
