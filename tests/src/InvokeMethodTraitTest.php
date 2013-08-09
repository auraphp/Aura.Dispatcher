<?php
namespace Aura\Invoker;

class InvokeMethodTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokeMethodTrait;
    
    public function testInvokeMethod_notCallable()
    {
        $object = new FakeObject;
        $this->setExpectedException('BadMethodCallException');
        $this->invokeMethod($object, 'noSuchMethod');
    }
    
    public function testInvokeMethod_object()
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
        $actual = $this->invokeMethod($closure, null, [
            'foo' => 'FOO',
            'bar' => 'BAR',
        ]);
        $this->assertSame($expect, $actual);
    }
}
