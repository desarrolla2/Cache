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

namespace Desarrolla2\Test\Cache;

use Desarrolla2\Cache\Memcached as MemcachedCache;
use Memcached;

/**
 * MemcachedTest
 */
class MemcachedTest extends AbstractCacheTest
{
    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'Only support keys up to 250 bytes'
    ];

    public function createSimpleCache()
    {
        if (!extension_loaded('memcached') || !class_exists('\Memcached')) {
            $this->markTestSkipped(
                'The Memcached extension is not available.'
            );
        }

        list($host, $port) = explode(':', CACHE_TESTS_MEMCACHED_SERVER) + [1 => 11211];

        $adapter = new Memcached();
        $adapter->addServer($host, (int)$port);

        if (!$adapter->flush()) {
            $this->markTestSkipped("Unable to flush Memcached; not running?");
        }

        return new MemcachedCache($adapter);
    }
}
