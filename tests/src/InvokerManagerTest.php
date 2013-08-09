<?php
namespace Aura\Invoker;

class InvokerManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $invoker_manager;
    
    protected $object_factory;
    
    protected function setUp()
    {
        $this->object_factory = new ObjectFactory([
            'closure' => function () {
                return function ($foo, $bar, $baz = 'baz') {
                    return "$foo $bar $baz";
                };
            },
            'object' => function () {
                return new FakeObject;
            },
        ]);
        
        $this->invoker_manager = new InvokerManager(
            $this->object_factory,
            'controller',
            'action'
        );
    }
    
    public function testGetObjectFactory()
    {
        $actual = $this->invoker_manager->getObjectFactory();
        $this->assertSame($this->object_factory, $actual);
    }
    
    public function testExec_objectViaParams()
    {
        $params = [
            'controller' => 'object',
            'action' => 'foobarbaz',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker_manager->exec($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_noObjectViaParams()
    {
        $params = [];
        $this->setExpectedException('Aura\Invoker\Exception\ObjectNotSpecified');
        $this->invoker_manager->exec($params);
    }
    
    public function testExec_noMethodViaParams()
    {
        $params = ['controller' => 'object'];
        $this->setExpectedException('Aura\Invoker\Exception\MethodNotSpecified');
        $this->invoker_manager->exec($params);
    }
    
    public function testExec_closureViaParams()
    {
        $params = [
            'controller' => 'closure',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker_manager->exec($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_explicitObjectAndMethod()
    {
        $this->invoker_manager->setObjectParam(null);
        $this->invoker_manager->setMethodParam(null);
        $object = new FakeObject;
        $params = ['foo' => 'FOO', 'bar' => 'BAR'];
        $actual = $this->invoker_manager->exec($params, $object, 'foobarbaz');
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_explicitClosure()
    {
        $this->invoker_manager->setObjectParam(null);
        $this->invoker_manager->setMethodParam(null);
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        $params = ['foo' => 'FOO', 'bar' => 'BAR'];
        $actual = $this->invoker_manager->exec($params, $closure);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
