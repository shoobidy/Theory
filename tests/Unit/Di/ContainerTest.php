<?php namespace Theory\Test;

use PHPUnit\Framework\TestCase;

use Theory\Tests\Unit\Di\Fixtures\{
    NullObject,
    ObjectParameter,
    DefaultParameter,
    ValueParameter,
    Recursive
};

use Theory\Di\Container;

use Theory\Di\Definition\{
    ObjectDefinition,
    ShareDefinition,
    ValueDefinition,
    ClosureDefinition
};
use Theory\Di\Exception\{
    UndefinedParameter,
    ClassNotShared,
    NotClosure
};

class ContainerTest extends TestCase
{
    public function test_defined_object_throws_undefined_parameter()
    {
        $this->expectException(UndefinedParameter::class);

        $class = ValueParameter::class;

        $definition = new ObjectDefinition($class);

        $container = new Container([$class => $definition]);
        $actual = $container->get($class);
    }
    
    public function test_resolve_default_parameter()
    {
        $class = DefaultParameter::class;

        $definition = new ObjectDefinition($class);

        $container = new Container([$class => $definition]);

        $expected = new $class();
        $actual = $container->get($class);

        $this->assertEquals($expected, $actual);
    }

    public function test_resolve_defined_parameter()
    {
        $class = ValueParameter::class;

        $definition = new ObjectDefinition($class);
        $definition->addParameterDefinition('value', new ValueDefinition('test'));

        $container = new Container([$class => $definition]);

        $expected = new $class('test');
        $actual = $container->get($class);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Container::get
     * Container::define
     * -- ObjectDefinition::resolve
     * -- ObjectDefinition::resolveParameters
     * -- ObjectDefinition::resolveParameter
     * -- -- Container::get($parameter)
     * 
     * return $object($parameter);
     */
    public function test_auto_resolve_parameter_from_defined_object()
    {
        $definition = new ObjectDefinition(ObjectParameter::class);

        $container = new Container([
            ObjectParameter::class => $definition
        ]);

        $expected = new ObjectParameter(new NullObject());
        $actual = $container->get(ObjectParameter::class);

        $this->assertEquals($expected, $actual);
    }

    public function test_auto_resolve_object_parameter()
    {
        $container = new Container();

        $expected = new ObjectParameter(new NullObject());
        $actual = $container->get(ObjectParameter::class);

        $this->assertEquals($expected, $actual);
    }

    public function test_throw_undefined_parameter_exception()
    {
        $this->expectException(UndefinedParameter::class);

        $container = new Container();
        $container->get(ValueParameter::class);
    }

    public function test_auto_resolve_recursively()
    {
        $container = new Container();

        $expected = new Recursive(new ObjectParameter(new NullObject()));
        $actual = $container->get(Recursive::class);

        $this->assertEquals($expected, $actual);
    }

    public function test_auto_resolve_default_parameter()
    {
        $container = new Container();

        $actual = $container->get(DefaultParameter::class);

        $this->assertEquals('test', $actual->param);
    }

    public function test_share_objects()
    {
        $nullObj = new ObjectDefinition(NullObject::class);
        $nullObj->share();

        $objParam = new ObjectDefinition(ObjectParameter::class);
        $objParam->addParameterDefinition('obj', new ShareDefinition(NullObject::class));

        $container = new Container([
            NullObject::class => $nullObj,
            ObjectParameter::class => $objParam
        ]);

        // create and add the object to the shared list
        $container->get(NullObject::class);

        $one = $container->get(ObjectParameter::class)->obj;
        $two = $container->get(ObjectParameter::class)->obj;

        $this->assertSame($one, $two);
    }

    public function test_throw_share_exception()
    {
        $this->expectException(ClassNotShared::class);

        $objParam = new ObjectDefinition(ObjectParameter::class);
        $objParam->addParameterDefinition('obj', new ShareDefinition(NullObject::class));

        $container = new Container([
            ObjectParameter::class => $objParam
        ]);

        $container->get(ObjectParameter::class)->obj;
    }

    public function test_resolve_closure()
    {
        $container = new Container([
            'ClosureTest' => new ClosureDefinition(function(){
                return new NullObject;
            })
        ]);

        $expected = new NullObject();
        $actual = $container->get('ClosureTest');

        $this->assertEquals($expected, $actual);
    }

    public function test_throw_not_closure()
    {
        $this->expectException(NotClosure::class);

        $container = new Container([
            'ClosureTest' => new ClosureDefinition(NullObject::class)
        ]);

        $container->get('ClosureTest');
    }
}