<?php
namespace Aura\Invoker;

class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $object_factory;
    
    protected function setUp()
    {
        $this->object_factory = new ObjectFactory;
    }
    
    public function test()
    {
        $object = function () {
            echo 'FOO!';
        };
        
        $factory = function () use ($object) {
            return $object;
        };
        
        $this->assertFalse($this->object_factory->has('foo'));
        $this->object_factory->set('foo', $factory);
        $this->assertTrue($this->object_factory->has('foo'));
        
        $actual = $this->object_factory->get('foo');
        $this->assertSame($factory, $actual);
        
        $foo = $this->object_factory->newInstance('foo');
        $this->assertSame($object, $foo);
    }
}
