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
use Desarrolla2\Cache\Adapter\File;

class FileTest extends \PHPUnit_Framework_TestCase
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
        $this->cache = new Cache(new File());
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
        $value = mktime();
        $this->cache->set('key', $value);
        $this->assertTrue($this->cache->has('key', $value));
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function getTest()
    {
        $value = mktime();
        $this->cache->set('key', $value);
        $this->assertEquals($this->cache->get('key'), $value);
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
        $value = mktime();
        $this->cache->set('key2', $value);
        $this->cache->delete('key2');
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