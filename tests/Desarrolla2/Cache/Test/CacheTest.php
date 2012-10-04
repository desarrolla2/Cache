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
     * @test
     * @expectedException \Desarrolla2\Cache\Exception\AdapterNotSetException
     */
    public function getAdapterThrowsExceptionTest()
    {
        $this->cache->getAdapter();
    }

}