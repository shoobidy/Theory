<?php namespace Theory\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Theory\Cache\ArrayCache;

class ArrayCacheTest extends TestCase
{
    public function testAdd()
    {
        $cache = new ArrayCache(__DIR__ . '/test.php');
        $cache->add('id', 'data');
        
        $this->assertSame('data', $cache->get('id'));
    }
    
    public function testWriteCacheToFile()
    {
        $cache = new ArrayCache(__DIR__ . '/test.php');
        $cache->add('id', 'data');
        $cache->save();
        
        $expected = [
            'id' => 'data'
        ];
        
        $actual = include __DIR__ . '/test.php';
        
        $this->assertSame($expected, $actual);
    }
    
    public function testClearCache()
    {
        $cache = new ArrayCache(__DIR__ . '/test.php');
        $cache->add('id', 'test');
        $cache->save();
        
        $cache->clear();
        
        $this->assertFalse($cache->has('id'));
        $this->assertFalse(is_file(__DIR__ . '/test.php'));
    }
    
    public function testCacheNeedsUpdate()
    {
        $cache = new ArrayCache(__DIR__ . '/test.php');
        $cache->add('id', 'test');
        $cache->save();
        
        $cache->add('a', 'b');
        
        $this->assertTrue($cache->isOld());
    }
    
    public function testCacheUpToDate()
    {
        $cache = new ArrayCache(__DIR__ . '/test.php');
        $cache->clear();
        
        $this->assertFalse($cache->isOld());
    }
}

