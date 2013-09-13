<?php
namespace Aura\Dispatcher;

class FactoryDispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $dispatcher;
    
    protected $objects;
    
    protected function setUp()
    {
        $this->objects = [
            'mock_base' => function () {
                return new MockBase;
            },
        ];
        
        $this->dispatcher = new FactoryDispatcher(
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
        
        $this->assertFalse($this->dispatcher->hasObject('foo'));
        
        $this->dispatcher->setObject('foo', $foo);
        $this->assertTrue($this->dispatcher->hasObject('foo'));
        
        $actual = $this->dispatcher->getObjectByName('foo');
        $this->assertInstanceOf('Aura\Dispatcher\MockBase', $actual);
        
        $actual = $this->dispatcher->getObjects();
        $expect = array_merge($this->objects, ['foo' => $foo]);
        $this->assertSame($expect, $actual);
        
        $bar = function () {
            return new MockExtended;
        };
        
        $this->dispatcher->addObjects(['bar' => $bar]);
        $actual = $this->dispatcher->getObjects();
        $expect = array_merge($this->objects, [
            'foo' => $foo,
            'bar' => $bar,
        ]);
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotDefined');
        $this->dispatcher->getObjectByName('NoSuchCallable');
    }
    
    public function testParams()
    {
        $this->dispatcher->setObjectParam('foo');
        $actual = $this->dispatcher->getObjectParam();
        $this->assertSame('foo', $actual);
        
        $this->dispatcher->setMethodParam('bar');
        $actual = $this->dispatcher->getMethodParam();
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
        $actual = $this->dispatcher->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testInvoke_objectNotSpecified()
    {
        $params = [];
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotSpecified');
        $this->dispatcher->__invoke($params);
    }
    
    public function testInvoke_objectNotDefined()
    {
        $params = ['controller' => 'undefined_object'];
        $this->setExpectedException('Aura\Dispatcher\Exception\ObjectNotDefined');
        $this->dispatcher->__invoke($params);
    }
    
    public function testInvoke_methodNotSpecified()
    {
        $params = ['controller' => 'mock_base'];
        $this->setExpectedException('Aura\Dispatcher\Exception\MethodNotSpecified');
        $this->dispatcher->__invoke($params);
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
        $actual = $this->dispatcher->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
