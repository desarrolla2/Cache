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

use Desarrolla2\Cache\Adapter\Test\AbstractCacheTest;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memory;

/**
 * 
 * Description of MemoryTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : MemoryTest.php , UTF-8
 * @date : Jun 30, 2013 , 4:22:06 PM
 */
class MemoryTest extends AbstractCacheTest
{

    /**
     * setup
     */
    public function setUp()
    {
        $this->cache = new Cache(new Memory());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('key1', 'value', 1, 0, 'value', true),
            array('key2', 'value', null, 0, 'value', true),
            array('key3', 'value', 1, 2, false, false),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return array(
            array('ttl', 100),
            array('limit', 100),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return array(
            array('ttl', 0, '\Desarrolla2\Cache\Exception\CacheException'),
            array('file', 100, '\Desarrolla2\Cache\Exception\CacheException'),
        );
    }

}