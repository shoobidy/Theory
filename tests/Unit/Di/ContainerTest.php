<?php namespace Theory\Test;

use PHPUnit\Framework\TestCase;
use Theory\Di\{
    Container,
    UndefinedParameterException,
    ShareException
};
use Theory\Di\Definition\{
    ObjectDefinition,
    ShareDefinition,
    ValueDefinition,
    ClosureDefinition
};
use Theory\Tests\Unit\Di\Fixtures\{
    NullObject,
    ObjectParameter,
    DefaultParameter,
    ValueParameter,
    Recursive
};

class ContainerTest extends TestCase
{
    public function test_auto_resolve_object_parameter()
    {
        $container = new Container();

        $expected = new ObjectParameter(new NullObject());
        $actual = $container->get(ObjectParameter::class);

        $this->assertEquals($expected, $actual);
    }

    public function test_throw_undefined_parameter_exception()
    {
        $this->expectException(UndefinedParameterException::class);

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
        $this->expectException(ShareException::class);

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
}