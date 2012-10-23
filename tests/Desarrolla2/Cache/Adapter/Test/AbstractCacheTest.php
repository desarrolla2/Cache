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
     * 
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type $ttl
     * @param type $sleep
     * @param type $return
     * @param type $return
     */
    public function hasTest($key, $value, $ttl, $sleep, $return, $has)
    {
        $this->cache->set($key, $value, $ttl);
        sleep($sleep);
        $this->assertEquals($has, $this->cache->has($key));
        $this->cache->delete($key);
    }

    /**
     * 
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type $ttl
     * @param type $sleep
     * @param type $return
     */
    public function getTest($key, $value, $ttl, $sleep, $return)
    {
        $this->cache->set($key, $value, $ttl);
        sleep($sleep);
        $this->assertEquals($return, $this->cache->get($key));
        $this->cache->delete($key);
    }

    /**
     * 
     * @test
     * @dataProvider dataProvider
     * @param string $key
     * @param string $value
     * @param type $ttl
     */
    public function deleteTest($key, $value, $ttl)
    {
        $this->cache->set($key, $value, $ttl);
        $this->cache->delete($key);
        $this->assertEquals(false, $this->cache->has($key));
        $this->assertEquals(null, $this->cache->get($key));
        $this->assertEquals(false, $this->cache->delete($key));
    }

    /**
     * 
     * @test
     * @dataProvider dataProviderForOptions
     * @param string $key
     * @param string $value
     * @param type $return
     */
    public function setOptionTest($key, $value)
    {
        $this->assertEquals(true, $this->cache->setOption($key, $value));
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
