<?php
namespace Aura\Dispatcher;

class InvokeClosureTraitTest extends \PHPUnit_Framework_TestCase
{
    use InvokeClosureTrait;
    
    public function testInvokeClosure_namedParams()
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
    
    public function testInvokeClosure_positionalParams()
    {
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        $expect = 'FOO BAR baz';
        $actual = $this->invokeClosure($closure, [
            0 => 'FOO',
            1 => 'BAR',
        ]);
        $this->assertSame($expect, $actual);
    }
    
    public function testInvokeClosure_directParams()
    {
        $closure = function (array $_params) {
            return implode(' ', $_params);
        };
        $expect = 'foo bar baz';
        $actual = $this->invokeClosure($closure, [
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ]);
        $this->assertSame($expect, $actual);
    }
    
    public function testInvokeClosure_paramNotSpecified()
    {
        $closure = function ($foo, $bar, $baz = 'baz') {
            return "$foo $bar $baz";
        };
        
        $this->setExpectedException(
            'Aura\Dispatcher\Exception\ParamNotSpecified',
            'Closure(1 : $bar)'
        );
        
        $this->invokeClosure($closure, [
                'foo' => 'foo',
                'baz' => 'baz',
        ]);
    }
}
