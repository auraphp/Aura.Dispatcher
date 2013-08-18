<?php
namespace Aura\Invoker;

class InvokerTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokerTrait;
    
    public function testInvokeMethod_notCallable()
    {
        $object = new FakeObject;
        $this->setExpectedException('BadMethodCallException');
        $this->invokeMethod($object, 'noSuchMethod');
    }
    
    public function testInvokeMethod()
    {
        $object = new FakeObject;
        $expect = 'FOO BAR baz';
        $actual = $this->invokeMethod($object, 'foobarbaz', [
            'foo' => 'FOO',
            'bar' => 'BAR',
        ]);
        $this->assertSame($expect, $actual);
    }

    public function testInvokeMethod_closure()
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
