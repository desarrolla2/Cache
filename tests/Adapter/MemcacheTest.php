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

use Desarrolla2\Cache\Memcache as MemcacheCache;
use Memcache as BaseMemcache;

/**
 * MemcacheTest
 */
class MemcacheTest extends AbstractCacheTest
{
    public function setUp()
    {
        parent::setup();
        if (!extension_loaded('memcache') || !class_exists('\Memcache')) {
            $this->markTestSkipped(
                'The Memcache extension is not available.'
            );
        }

        $adapter = new BaseMemcache();
        $adapter->addServer($this->config['memcache']['host'], $this->config['memcache']['port']);
        $this->cache = new MemcacheCache($adapter);
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return [
            ['ttl', 0, '\Desarrolla2\Cache\Exception\InvalidArgumentException'],
            ['file', 100, '\Desarrolla2\Cache\Exception\InvalidArgumentException'],
        ];
    }
}
