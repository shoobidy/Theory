<?php namespace Test\Unit\Autoload;

use PHPUnit\Framework\TestCase;
use Test\Unit\Autoload\Fixture\TestClass;
use Theory\Autoload\Autoloader;

include '../../../src/Autoload/Autoloader.php';

/**
 * @runTestsInSeparateProcesses
 */
class AutoloaderTest extends TestCase
{
    public function testSingleNamespaceId()
    {
        $autoloader = new Autoloader(['Test' => dirname(dirname(__DIR__))]);

        $autoloader->load(TestClass::class);

        $this->assertTrue(class_exists(TestClass::class, false));
    }

    public function testCompoundNamespaceId()
    {
        $autoloader = new Autoloader(['Test/Unit' => dirname(dirname(__DIR__)) . '/Unit']);

        $autoloader->load(TestClass::class);

        $this->assertTrue(class_exists(TestClass::class, false));
    }

    public function testUndefinedNamespace()
    {
        $autoloader = new Autoloader([]);

        $this->assertFalse($autoloader->load(TestClass::class)); 
    }

    public function testMissingFile()
    {
        $autoloader = new Autoloader(['Test' => 'path/that/does/not/exist']);

        $this->assertFalse($autoloader->load(TestClass::class)); 
    }

    public function testNormalizeNamespaceSeparators()
    {
        $autoloader = new Autoloader([
            '\\Test\\Unit\\' => dirname(dirname(__DIR__)) . '/Unit'
        ]);

        $autoloader->load(TestClass::class);

        $this->assertTrue(class_exists(TestClass::class, false));
    }

    public function testRemoveTrailingDirectorySeparators()
    {
        $autoloader = new Autoloader([
            'Test' => dirname(dirname(__DIR__)) . '/'
        ]);

        $autoloader->load(TestClass::class);

        $this->assertTrue(class_exists(TestClass::class, false));
    }
}