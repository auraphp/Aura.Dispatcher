<?php
namespace Aura\Invoker;

class InvokerTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokerTrait;
    
    public function testInvokeMethod_notCallable()
    {
        $object = new MockBase;
        $this->setExpectedException('Aura\Invoker\Exception\MethodNotDefined');
        $object->exec('noSuchMethod');
    }
    
    public function testInvokeMethod_public()
    {
        // works on base object
        $object = new MockBase;
        $expect = 'FOO BAR baz';
        $actual = $object->exec(
            'publicMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
        $this->assertSame($expect, $actual);
        
        // works on extended object
        $object = new MockExtended;
        $expect = 'FOO BAR baz';
        $actual = $object->exec(
            'publicMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
        $this->assertSame($expect, $actual);
        
    }

    public function testInvokeMethod_protected()
    {
        // works on base object
        $object = new MockBase;
        $expect = 'FOO BAR baz';
        $actual = $object->exec(
            'protectedMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
        $this->assertSame($expect, $actual);
        
        // works on extended object
        $object = new MockExtended;
        $expect = 'FOO BAR baz';
        $actual = $object->exec(
            'protectedMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
        $this->assertSame($expect, $actual);
        
        // fails on external call
        $object = new MockExtended;
        $expect = 'FOO BAR baz';
        $this->setExpectedException('Aura\Invoker\Exception\MethodNotAccessible');
        $actual = $this->invokeMethod(
            $object,
            'protectedMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
    }
    
    public function testInvokeMethod_private()
    {
        // works on base object
        $object = new MockBase;
        $expect = 'FOO BAR baz';
        $actual = $object->exec(
            'privateMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
        $this->assertSame($expect, $actual);
        
        // fails on extended object
        $object = new MockExtended;
        $expect = 'FOO BAR baz';
        $this->setExpectedException('Aura\Invoker\Exception\MethodNotAccessible');
        $actual = $object->exec(
            'privateMethod',
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
            ]
        );
    }
    
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
