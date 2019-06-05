<?php namespace Theory\Tests\Autoload;

use PHPUnit\Framework\TestCase;
use Theory\Autoload\Autoloader;

class AutoloaderTest extends TestCase
{
    private $cases = [
        1 => [
            'namespace' => 'Acme\Log\Writer\File_Writer',
            'prefix' => 'Acme\Log\Writer',
            'dir' => '/acme-log-writer/lib/',
            'expected' => '/acme-log-writer/lib/File_Writer.php'
        ],
        2 => [
            'namespace' => 'Aura\Web\Response\Status',
            'prefix' => 'Aura\Web',
            'dir' => '/path/to/aura-web/src/',
            'expected' => '/path/to/aura-web/src/Response/Status.php'
        ],
        3 => [
            'namespace' => 'Symfony\Core\Request',
            'prefix' => 'Symfony\Core',
            'dir' => '/vendor/Symfony/Core/',
            'expected' => '/vendor/Symfony/Core/Request.php'
        ],
        4 => [
            'namespace' => 'Zend\Acl',
            'prefix' => 'Zend',
            'dir' => '/usr/includes/Zend/',
            'expected' => '/usr/includes/Zend/Acl.php'
        ],
        5 => [
            'namespace' => 'Theory\Dispatching\Dispatcher',
            'prefix' => 'Theory',
            'dir' => '/www/projects/theory/src',
            'expected' => '/www/projects/theory/src/Dispatching/Dispatcher.php'
        ]
    ];
	
	public function testAutoload()
    {
		$theory = '/www/apps/theory/lib/theory/src';
		
		include $theory . '/Autoload/Autoloader.php';
		include $theory . '/Cache/CacheInterface.php';
		include $theory . '/Cache/ArrayCache.php';
		include $theory . '/FileSystem/File.php';
		
		$registry = [];
		
		foreach ($this->cases as $case) {
			$prefix = $case['prefix'];
			$dir = $case['dir'];
			
			$registry[$prefix] = $dir;
		}
		
		$a = new Autoloader(
			$registry,
			'',
			new Theory\Cache\ArrayCache(new File($theory . '/Autoload/cache/registry.php'))
		);
			
        foreach ($this->cases as $case) {
            $actual = $a->load($case['namespace']);

            $this->assertSame($actual, $case['expected']);
        }
    }

    /*public function testAutoload()
    {
        foreach ($this->cases as $case) {
            $a = new Autoloader([$case['prefix'] => $case['dir']]);
            $actual = $a->load($case['namespace']);

            $this->assertSame($actual, $case['expected']);
        }
    }*/
}
