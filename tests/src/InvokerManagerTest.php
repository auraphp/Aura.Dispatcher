<?php
namespace Aura\Invoker;

class InvokerManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $invoker;
    
    protected $factories;
    
    protected function setUp()
    {
        $this->factories = [
            'closure' => function () {
                return function ($foo, $bar, $baz = 'baz') {
                    return "$foo $bar $baz";
                };
            },
            'object' => function () {
                return new MockBase;
            },
        ];
        
        $this->invoker = new InvokerManager(
            $this->factories,
            'controller',
            'action'
        );
    }
    
    public function testFactories()
    {
        $foo_factory = function () {
            return function () {
                echo 'FOO!';
            };
        };
        
        $this->assertFalse($this->invoker->hasFactory('foo'));
        
        $this->invoker->setFactory('foo', $foo_factory);
        $this->assertTrue($this->invoker->hasFactory('foo'));
        
        $actual = $this->invoker->getFactory('foo');
        $this->assertSame($foo_factory, $actual);
        
        $actual = $this->invoker->getFactories();
        $expect = array_merge($this->factories, ['foo' => $foo_factory]);
        $this->assertSame($expect, $actual);
        
        $bar_factory = function () {
            return function () {
                echo 'BAR!';
            };
        };
        
        $this->invoker->addFactories(['bar' => $bar_factory]);
        $actual = $this->invoker->getFactories();
        $expect = array_merge($this->factories, [
            'foo' => $foo_factory,
            'bar' => $bar_factory,
        ]);
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Invoker\Exception\FactoryNotDefined');
        $this->invoker->getFactory('NoSuchFactory');
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
    
    public function testExec_objectViaParams()
    {
        $params = [
            'controller' => 'object',
            'action' => 'publicMethod',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->exec($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_noObjectViaParams()
    {
        $params = [];
        $this->setExpectedException('Aura\Invoker\Exception\ObjectNotSpecified');
        $this->invoker->exec($params);
    }
    
    public function testExec_noMethodViaParams()
    {
        $params = ['controller' => 'object'];
        $this->setExpectedException('Aura\Invoker\Exception\MethodNotSpecified');
        $this->invoker->exec($params);
    }
    
    public function testExec_closureViaParams()
    {
        $params = [
            'controller' => 'closure',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->exec($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_explicitObjectAndMethod()
    {
        $this->invoker->setObjectParam(null);
        $this->invoker->setMethodParam(null);
        $object = new MockBase;
        $params = ['foo' => 'FOO', 'bar' => 'BAR'];
        $actual = $this->invoker->exec($params, $object, 'publicMethod');
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_explicitClosure()
    {
        $this->invoker->setObjectParam(null);
        $this->invoker->setMethodParam(null);
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        $params = ['foo' => 'FOO', 'bar' => 'BAR'];
        $actual = $this->invoker->exec($params, $closure);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_closureParamValue()
    {
        $params = [
            'controller' => function ($foo, $bar, $baz = 'baz') {
                return "$foo $bar $baz";
            },
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->exec($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
