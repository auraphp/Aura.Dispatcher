<?php
namespace Aura\Dispatcher;

class ClosureDispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $invoker;
    
    protected $objects;
    
    protected function setUp()
    {
        $this->objects = [
            'closure' => function ($foo, $bar, $baz = 'baz') {
                return "$foo $bar $baz";
            },
        ];
        
        $this->invoker = new ClosureDispatcher(
            $this->objects,
            'controller',
            'action'
        );
    }
    
    public function testInvoke()
    {
        $params = [
            'controller' => 'closure',
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
    
    public function testInvoke_closureInParams()
    {
        $params = [
            'controller' => function ($foo, $bar, $baz = 'baz') {
                return "$foo $bar $baz";
            },
            'foo' => 'FOO',
            'bar' => 'BAR',
        ];
        $actual = $this->invoker->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
