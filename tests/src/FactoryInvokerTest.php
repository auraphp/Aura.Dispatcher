<?php
namespace Aura\Dispatcher;

class FactoryDispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $invoker;
    
    protected $objects;
    
    protected function setUp()
    {
        $this->objects = [
            'mock_base' => function () {
                return new MockBase;
            },
        ];
        
        $this->invoker = new FactoryDispatcher(
            $this->objects,
            'controller',
            'action'
        );
    }
    
    public function testGetSetHasEtc()
    {
        $foo = function () {
            return new MockBase;
        };
        
        $this->assertFalse($this->invoker->hasObject('foo'));
        
        $this->invoker->setObject('foo', $foo);
        $this->assertTrue($this->invoker->hasObject('foo'));
        
        $actual = $this->invoker->getObjectByName('foo');
        $this->assertInstanceOf('Aura\Dispatcher\MockBase', $actual);
        
        $actual = $this->invoker->getObjects();
        $expect = array_merge($this->objects, ['foo' => $foo]);
        $this->assertSame($expect, $actual);
        
        $bar = function () {
            return new MockExtended;
        };
        
        $this->invoker->addObjects(['bar' => $bar]);
        $actual = $this->invoker->getObjects();
        $expect = array_merge($this->objects, [
            'foo' => $foo,
            'bar' => $bar,
        ]);
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotDefined');
        $this->invoker->getObjectByName('NoSuchCallable');
    }
    
    public function testParams()
    {
        $this->invoker->setObjectParam('foo');
        $actual = $this->invoker->getObjectParam();
        $this->assertSame('foo', $actual);
        
        $this->invoker->setMethodParam('bar');
        $actual = $this->invoker->getMethodParam();
        $this->assertSame('bar', $actual);
    }
    
    public function testInvoke()
    {
        $params = [
            'controller' => 'mock_base',
            'action' => 'publicMethod',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testInvoke_objectNotSpecified()
    {
        $params = [];
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotSpecified');
        $this->invoker->__invoke($params);
    }
    
    public function testInvoke_objectNotDefined()
    {
        $params = ['controller' => 'undefined_object'];
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotDefined');
        $this->invoker->__invoke($params);
    }
    
    public function testInvoke_methodNotSpecified()
    {
        $params = ['controller' => 'mock_base'];
        $this->setExpectedException('Aura\Dispatcher\Exception\MethodNotSpecified');
        $this->invoker->__invoke($params);
    }
    
    public function testInvoke_factoryInParams()
    {
        $params = [
            'controller' => function () {
                return new MockBase;
            },
            'action' => 'publicMethod',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
