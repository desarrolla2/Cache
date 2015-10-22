<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace Desarrolla2\Test\Cache\Adapter;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memcached;

/**
 * MemcachedTest
 */
class MemcachedTest extends AbstractCacheTest
{
    public function setUp()
    {
        parent::setup();
        if (!extension_loaded('memcached') || !class_exists('\Memcached')) {
            $this->markTestSkipped(
                'The Memcached extension is not available.'
            );
        }

        $adapter = new Memcached();
        $this->cache = new Cache($adapter);
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return [
            ['ttl', 0, '\Desarrolla2\Cache\Exception\CacheException'],
            ['file', 100, '\Desarrolla2\Cache\Exception\CacheException'],
        ];
    }
}
