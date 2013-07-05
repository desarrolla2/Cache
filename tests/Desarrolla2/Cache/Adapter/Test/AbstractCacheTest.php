<?php

/**
 * This file is part of the Cache proyect.
 *
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter\Test;

/**
 *
 * Description of AbstracCacheTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : AbstracCacheTest.php , UTF-8
 * @date : Oct 23, 2012 , 10:57:28 PM
 */
abstract class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Desarrolla2\Cache\Cache
     */
    protected $cache;

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('key1', 'value', 1),
            array('key2', 'value', 100),
            array('key3', 'value', null),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return array(
            array('ttl', 100),
        );
    }

    /**
     *
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type   $ttl
     */
    public function hasTest($key, $value, $ttl)
    {
        $this->assertFalse($this->cache->has($key));
        $this->assertNull($this->cache->set($key, $value, $ttl));
        $this->assertTrue($this->cache->has($key));
    }

    /**
     *
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type   $ttl
     */
    public function getTest($key, $value, $ttl)
    {
        $this->cache->set($key, $value, $ttl);
        $this->assertEquals($value, $this->cache->get($key));
    }

    /**
     *
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type   $ttl
     */
    public function deleteTest($key, $value, $ttl)
    {
        $this->cache->set($key, $value, $ttl);
        $this->assertNull($this->cache->delete($key));
        $this->assertFalse($this->cache->has($key));
    }

    /**
     *
     * @test
     */
    public function hasWithTtlExpiredTest()
    {
        $key = 'key1';
        $value = 'value1';
        $ttl = 1;
        $this->cache->set($key, $value, $ttl);
        sleep($ttl + 1);
        $this->assertFalse($this->cache->has($key));
    }

    /**
     *
     * @test
     * @dataProvider dataProviderForOptions
     * @param string $key
     * @param string $value
     * @param type   $return
     */
    public function setOptionTest($key, $value)
    {
        $this->assertTrue($this->cache->setOption($key, $value));
    }

    /**
     * @test
     * @dataProvider dataProviderForOptionsException
     * @param string $key
     * @param string $value
     */
    public function setOptionTestException($key, $value, $expectedException)
    {
        $this->setExpectedException($expectedException);
        $this->cache->setOption($key, $value);
    }

}
