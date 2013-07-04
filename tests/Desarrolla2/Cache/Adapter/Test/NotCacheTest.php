<?php

/**
 * This file is part of the perrosygatos proyect.
 *
 * Description of CacheTest
 *
 * @author : Daniel González Cerviño <daniel.gonzalez@ideup.com>
 * @file : CacheTest.php , UTF-8
 * @date : Sep 4, 2012 , 3:49:01 PM
 */

namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\NotCache;

class NotCacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Desarrolla2\Cache\Cache
     */
    protected $cache;

    /**
     * setup
     */
    public function setUp()
    {
        $this->cache = new Cache(new NotCache());
    }

    /**
     * @return type
     */
    public function dataProvider()
    {
        return array(
            array(),
        );
    }

    /**
     * @test
     * @dataProvider dataProvider

     */
    public function hasTest()
    {
        $this->cache->set('key', 'value');
        $this->assertFalse($this->cache->has('key', 'value'));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function getTest()
    {
        $this->cache->set('key', 'value');
        $this->assertFalse($this->cache->get('key'));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function setTest()
    {
        $this->assertNull($this->cache->set('key', 'value'));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function deleteTest()
    {
        $this->assertNull($this->cache->delete('key'));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function setOptionTest()
    {
        $this->cache->setOption('ttl', 3600);
    }

}
