<?php namespace Theory\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Theory\Autoload\Autoloader;

use Theory\A;
use Theory\Tests\LongerNamespace;

include_once ROOT . '/src/Autoload/Autoloader.php';

/**
 * @runTestsInSeparateProcesses
 */
class AutoloaderTest extends TestCase
{
    public function test_load_includes_file_and_returns_true_on_success()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Theory', __DIR__ . '/Fixture');

        $this->assertTrue($autoloader->load(A::class));
        $this->assertTrue(class_exists(A::class, false));
    }

    public function test_load_returns_false_when_file_not_found()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Theory', 'Invalid/File/Path');

        $this->assertFalse($autoloader->load(A::class));
        $this->assertFalse(class_exists(A::class, false));
    }

    public function test_load_returns_false_when_namespace_not_found()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Invalid\\Namespace', __DIR__ . '/Fixture');

        $this->assertFalse($autoloader->load(A::class));
        $this->assertFalse(class_exists(A::class, false));
    }

    public function test_register_autoloader()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Theory', __DIR__ . '/Fixture');
        $autoloader->register();

        $this->assertTrue(class_exists(A::class));
    }

    public function test_prioritize_longer_namespace()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Theory', 'Should/Not/Be/Used');
        $autoloader->addNamespace('Theory\\Tests', __DIR__ . '/Fixture');
        $autoloader->register();

        $this->assertTrue(class_exists(LongerNamespace::class));
    }

    public function test_load_class_with_forward_slash_separators()
    {
        $autoloader = new Autoloader();
        $autoloader->addNamespace('Theory/Tests', __DIR__ . '/Fixture');
        $autoloader->register();
        
        $this->assertTrue(class_exists(LongerNamespace::class));
    }
}