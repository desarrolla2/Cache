<?php

/**
 * This file is part of the Cache project.
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
use Desarrolla2\Cache\Adapter\Memcache;

/**
 *
 * Description of MemoryTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : MemoryTest.php , UTF-8
 * @date : Jun 30, 2013 , 4:22:06 PM
 */
// extends AbstractCacheTest

class MemCacheTest extends AbstractCacheTest
{

    /**
     * setup
     */
    public function setUp()
    {
        parent::setup();
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped(
                'The Memcache extension is not available.'
            );
        }
        $adapter = new MemCache();
        $adapter->addServer('localhost', 11211);
        $this->cache = new Cache($adapter);
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
