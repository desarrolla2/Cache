<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace Desarrolla2\Test\Cache;

use Desarrolla2\Cache\Apcu as ApcuCache;

/**
 * CacheTest
 */
class AcpuCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Desarrolla2\Cache\Cache
     */
    protected $cache;

    public function setUp()
    {
        $this->cache = new ApcuCache();
        $this->testdata = [
            'null' => null,
            'boolean' => TRUE,
            'integer' => 1,
            'zero' => 0,
            'float' => 1.23, 
            'minus' => -1.23,
            'string' => 'Some test string',
            'array' => [FALSE, 3, 'strig'],
            'array_assoc' => ['key1' => 'value1', 'key2' => 2],
            'object' => new \stdClass()
        ];
    }

    public function cacheProvider()
    {
        return [
            ['null', null],
            
            ['float', 1.23] ,
            ['minus', -1.23],
            ['string', 'Some test string'],
            ['array', [FALSE, 3, 'string']],
            ['array_assoc', ['key1' => 'value1', 'key2' => 2]],
            ['object', new \stdClass()],
        ];
    }

    public function testGetDefaultOptions()
    {
        $this->assertEquals(3600, $this->cache->getOption('ttl'));
        $this->assertEquals('', $this->cache->getOption('prefix'));
    }

    public function testSetOptions()
    {
        $this->cache->setOption('ttl', 1);
        $this->assertEquals(1, $this->cache->getOption('ttl'));
    }

    /**
     * This test are test return null from non existing key
     */
    public function testGetEmptyKey()
    {
        $this->assertSame(null, $this->cache->get('NotSettedKey'));
    }

    /**
     * This test check key names are accepteble
     * @expectedException InvalidArgumentException
     */
    public function testGetIncorrectKey()
    {
        $this->cache->set('NonValidate@key', 'value');
    }

    /**
     * This test are simply test if all data returned
     * @dataProvider cacheProvider
     */
    public function testSetKey($key, $value)
    {
        $this->cache->set($key, $value);
        $this->assertSame($value, $this->cache->get($key));
    }

    /**
     * Test get not existing key with default value
     */
    public function testGetKeyDefaultValue()
    {
        $this->assertSame('default', $this->cache->get('key', 'value'));
    }

    /**
     * This test are simply test if all data returned null if ttl end
     */
    public function testTtlTime()   
    {
        $this->cache->set('key', 'value', 1);
        sleep(2);
        $this->assertSame(null, $this->cache->get('key'));
    }

    /**
     * This test check key names are accepteble
     * @expectedException InvalidArgumentException
     */
    public function testSetIncorrectKey()
    {
        $this->cache->set('NonValidate@key', 'value');
    }

    /**
     * This test are test has function
     * @dataProvider cacheProvider
     */
    public function testHasKey($key, $value)
    {
        $this->cache->set($key, $value);
        $this->assertSame(true, $this->cache->get($key));
    }

    /**
     * This test are test has function
     */
    public function testNonHasKey()
    {
        $this->assertSame(false, $this->cache->get('NotSettedKey'));
    }
    
    /**
     * This test check key names are accepteble
     * @expectedException InvalidArgumentException
     */
    public function testHasIncorrectKey()
    {
        $this->cache->has('NonValidate@key');
    }

    /**
     * This function test remove key from cache
     */
    public function testRemoveKey()
    {
        $this->cache->set('key', 'value', 1);
        $this->cache->delete('key');
        $this->assertSame(false, $this->cache->has('key'));
        $this->assertSame(null, $this->cache->get('key'));
    }

    /**
     * This function test clear cache
     */
    public function testClearCache()
    {
        $this->cache->set('key', 'value', 1);
        $this->cache->clear();
        $this->assertSame(false, $this->cache->has('key'));
        $this->assertSame(null, $this->cache->get('key'));
    }

    /**
     * Test set multipe values to the cache
     */
    
    public function testMultipleSet()
    {
        $this->assertSame('array' , gettype($this->cache->setMultiple($this->testData)));
    }

    /**
     * Test set multipe values with wrong key
     * @expectedException InvalidArgumentException
     */
    public function testMultipleSetIncorrectKey()
    {
        $array = array_merge($this->testData, ['NonValidate@key' => TRUE]);
        $this->assertSame('array' , gettype($this->cache->setMultiple($array)));
    }

    /**
     * Test get multipe values to the cache
     */
    public function testMultipleGet()
    {
        $this->cache->setMultiple($this->testData);
        $this->assertSame($this->testData , $this->get->getMultiple($this->testData));
    }

    /**
     * Test get multipe values to the cache
     */
    public function testMultipleGetIncorrectKey()
    {
        $array = array_merge(array_keys($this->testData), ['NonValidate@key']);
        $this->assertSame($this->testData , $this->get->getMultiple($array));
    }
}
