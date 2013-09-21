<?php
namespace Aura\Dispatcher;

class InvokeClosureTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokeClosureTrait;
    
    public function testInvokeClosure_namedParams()
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
    
    public function testInvokeClosure_positionalParams()
    {
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        $expect = 'FOO BAR baz';
        $actual = $this->invokeClosure($closure, [
            0 => 'FOO',
            1 => 'BAR',
        ]);
        $this->assertSame($expect, $actual);
    }
    
}
