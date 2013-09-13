<?php
namespace Aura\Dispatcher;

class ClosureDispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $dispatcher;
    
    protected $objects;
    
    protected function setUp()
    {
        $this->objects = [
            'closure' => function ($foo, $bar, $baz = 'baz') {
                return "$foo $bar $baz";
            },
        ];
        
        $this->dispatcher = new ClosureDispatcher(
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
        $actual = $this->dispatcher->__invoke($params);
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
        $actual = $this->dispatcher->__invoke($params);
        $expect = 'FOO BAR baz';
        $this->assertSame($expect, $actual);
    }
}
