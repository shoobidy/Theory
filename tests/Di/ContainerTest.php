<?php namespace Theory\Tests\Di;

use PHPUnit\Framework\TestCase;
use Theory\Di\Container;
use Theory\Cache\ArrayCache;

use Theory\Tests\Di\TestObjects\Obj;
use Theory\Tests\Di\TestObjects\Autowire;
use Theory\Tests\Di\TestObjects\AutowireRecursive;
use Theory\Tests\Di\TestObjects\AnInterface;
use Theory\Tests\Di\TestObjects\ImplementsInterfaceOne;
use Theory\Tests\Di\TestObjects\ImplementsInterfaceTwo;
use Theory\Tests\Di\TestObjects\ReplaceInterface;
use Theory\Tests\Di\TestObjects\ChildObject;


class ContainerTest extends TestCase
{
    public function getContainer()
    {
        return new Container(new ArrayCache(__DIR__ . '/cache.php'));
    }

    public function testCreateObject()
    {
        $container = $this->getContainer();
        $this->assertEquals(new Obj, $container->create(Obj::class));
    }
    
    
    public function testAutowireConstructor()
    {
        $container = $this->getContainer();

        $this->assertEquals(
            new Autowire(new Obj, new Obj),
            $container->create(Autowire::class)
        );
    }

    
    public function testAutowireConstructorRecursive()
    {
        $container = $this->getContainer();

        $this->assertEquals(
            new AutowireRecursive(
                    new Autowire(new Obj, new Obj),
                    new Autowire(new Obj, new Obj)
            ),
            $container->create(AutowireRecursive::class)
        );
    }

    public function testReplaceInterface()
    {
        $container = $this->getContainer();
        
        $container->addRule(AnInterface::class, [
            'class' => ImplementsInterfaceOne::class
        ]);

        $this->assertEquals(
            new ReplaceInterface(new ImplementsInterfaceOne),
            $container->get(ReplaceInterface::class)
        );
    }

    // Test that the more specific configuration takes priority over the more
    // general configuration
    public function testReplaceInterfacePriority()
    {
        $container = $this->getContainer();
        
        $container->addRules([
            // general config (applies to any instance of AnInterface)
            AnInterface::class => [
                'class' => ImplementsInterfaceOne::class
            ],

            // specific config (only applies to the class ReplaceInterface)
            ReplaceInterface::class => [
                'params' => [
                    AnInterface::class => ImplementsInterfaceTwo::class
                ]
            ]
        ]);

        $this->assertEquals(
            new ReplaceInterface(new ImplementsInterfaceTwo),
            $container->get(ReplaceInterface::class)
        );
    }

    // Test autowiring/calling methods
    public function testCallMethod()
    {
        $container = $this->getContainer();
        
        $container->addRule(Obj::class, [
                'call' => [
                    'set' => ['value' => 'test']
                ]
        ]);

        $expected = new Obj;
        $expected->set('test');

        $this->assertEquals($expected, $container->get(Obj::class));
    }

    public function testCallParentMethod()
    {
        $container = $this->getContainer();
        
        $container->addRule(Obj::class, [
                'call' => [
                    'set' => ['value' => 'test']
                ]
        ]);

        $expected = new ChildObject;
        $expected->set('test');
        
        $this->assertEquals($expected, $container->get(ChildObject::class));
    }
    
    public function testConfigByMethod()
    {
        $container = $this->getContainer();
        
        $this->assertEquals(
            new ReplaceInterface(new ImplementsInterfaceOne),
            $container->get(
                ReplaceInterface::class,
                ['params' => [AnInterface::class => ImplementsInterfaceOne::class]]
            )
        );
    }
    
    public function testSharedInstance()
    {
        $container = $this->getContainer();
        
        $this->assertSame(
                $container->create(Obj::class, ['share' => true]),
                $container->get(Obj::class)
        );
    }
}
