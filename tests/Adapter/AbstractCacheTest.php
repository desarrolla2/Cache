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

namespace Desarrolla2\Test\Cache\Adapter;

/**
 * AbstractCacheTest
 */
abstract class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Desarrolla2\Cache\Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $config = [];

    public function setup()
    {
        $configurationFile = realpath(__DIR__.'/../').'/config.json';

        if (!is_file($configurationFile)) {
            throw new \Exception(' Configuration file not found in "'.$configurationFile.'" ');
        }
        $this->config = json_decode(file_get_contents($configurationFile), true);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['key1', 'value1', 1],
            ['key2', 'value2', 100],
            ['key3', 'value3', null],
            ['key4', true, null],
            ['key5', false, null],
            ['key6', [], null],
            ['key7', new \DateTime(), null],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return [
            ['ttl', 100],
        ];
    }

    /**
     *
     * @dataProvider dataProvider
     *
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     */
    public function testHas($key, $value, $ttl)
    {
        $this->assertNull($this->cache->delete($key));
        $this->assertFalse($this->cache->has($key));
        $this->assertNull($this->cache->set($key, $value, $ttl));
        $this->assertTrue($this->cache->has($key));
    }

    /**
     *
     * @dataProvider dataProvider
     *
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     */
    public function testGet($key, $value, $ttl)
    {
        $this->cache->set($key, $value, $ttl);
        $this->assertEquals($value, $this->cache->get($key));
    }

    /**
     *
     * @dataProvider dataProvider
     *
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     */
    public function testDelete($key, $value, $ttl)
    {
        $this->cache->set($key, $value, $ttl);
        $this->assertNull($this->cache->delete($key));
        $this->assertFalse($this->cache->has($key));
    }

    /**
     * @dataProvider dataProviderForOptions
     *
     * @param string $key
     * @param mixed  $value
     */
    public function testSetOption($key, $value)
    {
        $this->assertTrue($this->cache->setOption($key, $value));
    }

    /**
     * @dataProvider dataProviderForOptionsException
     *
     * @param string     $key
     * @param mixed      $value
     * @param \Exception $expectedException
     */
    public function testSetOptionException($key, $value, $expectedException)
    {
        $this->setExpectedException($expectedException);
        $this->cache->setOption($key, $value);
    }

    public function testHasWithTtlExpired()
    {
        $this->cache->set('key1', 'value1', 1);
        sleep(2);
        $this->assertFalse($this->cache->has('key1'));
    }
}
