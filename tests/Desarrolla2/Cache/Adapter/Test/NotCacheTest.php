<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of NotCacheTest
 *
 * @author : Daniel González Cerviño <daniel.gonzalez@ideup.com>
 * @file : NotCacheTest.php , UTF-8
 * @date : Sep 5, 2012 , 6:28:37 PM
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
        $this->cache = new Cache();
        $this->cache->setAdapter(new NotCache());
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function setTest()
    {
        $this->cache->set('key', 'value');
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function deleteTest()
    {
        $this->cache->delete('key');
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function hasTest()
    {
        $this->cache->has('key');
    }

    /**
     * @test
     * @expectedException AdapterNotSetException
     */
    public function getAdapterThrowsExceptionTest()
    {
        $this->cache->getAdapter();
    }
}