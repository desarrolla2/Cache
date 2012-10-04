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

namespace Desarrolla2\Cache\Test;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter;
use Desarrolla2\Cache\Adapter\AdapterInterface;

class CacheTest extends \PHPUnit_Framework_TestCase
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
    }

    /**
     * @return type
     */
    public function dataProvider()
    {
        return array(
            array(new Adapter\NotCache()),
            array(new Adapter\File()),
        );
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param \Desarrolla2\Cache\Adapter\AdapterInterface $adapter
     */
    public function setTest(AdapterInterface $adapter)
    {
        $this->cache->setAdapter($adapter);
        $this->cache->set('key', 'value');
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param \Desarrolla2\Cache\Adapter\AdapterInterface $adapter
     */
    public function deleteTest(AdapterInterface $adapter)
    {
        $this->cache->setAdapter($adapter);
        $this->cache->delete('key');
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param \Desarrolla2\Cache\Adapter\AdapterInterface $adapter
     */
    public function hasTest(AdapterInterface $adapter)
    {
        $this->cache->setAdapter($adapter);
        $this->cache->has('key');
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param \Desarrolla2\Cache\Adapter\AdapterInterface $adapter
     */
    public function setOptionTest(AdapterInterface $adapter)
    {
        $this->cache->setAdapter($adapter);
        $this->cache->setOption('key', 'value');
    }

    /**
     * @test
     * @expectedException \Desarrolla2\Cache\Exception\AdapterNotSetException
     */
    public function getAdapterThrowsExceptionTest()
    {
        $this->cache->getAdapter();
    }

}