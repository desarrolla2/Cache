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
use Desarrolla2\Cache\Adapter\Apc;
use Desarrolla2\Cache\Adapter\AdapterInterface;

class ApcCacheTest extends \PHPUnit_Framework_TestCase
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
        if (!extension_loaded('apc') || !ini_get('apc.enable_cli')) {

            $this->markTestSkipped(
                    'The APC extension is not available.'
            );
        }
        $this->cache = new Cache();
        $this->cache = new Cache(new Apc());
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
    public function setTest(AdapterInterface $adapter)
    {
        $this->cache->set('key', 'value');
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function getTest(AdapterInterface $adapter)
    {
        $this->cache->set('key', 'value');
        $this->assertEcuals($this->cache->get('key'), 'value');
    }

    /**
     * @test
     * @dataProvider dataProvider

     */
    public function deleteTest(AdapterInterface $adapter)
    {
        $this->cache->delete('key');
    }

    /**
     * @test
     * @dataProvider dataProvider

     */
    public function hasTest(AdapterInterface $adapter)
    {
        $this->cache->has('key');
        $this->assertFalse($this->cache->has('key', 'value'));
    }

    /**
     * @test
     * @dataProvider dataProvider

     */
    public function setOptionTest(AdapterInterface $adapter)
    {
        $this->cache->setOption('key', 'value');
    }

}