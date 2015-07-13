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
namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memcached;

/**
 * MemcachedTest
 */
class MemcachedTest extends AbstractCacheTest {

    public function setUp() {
        parent::setup();
        if (!extension_loaded('memcached') || !class_exists('\Memcached')) {
            $this->markTestSkipped(
                'The Memcached extension is not available.'
            );
        }

        $data = [
            [
                'host'   => 'localhost',
                'port'   => 11211,
                'weight' => 0
            ]
        ];

        $adapter = new Memcached($data);
        $this->cache = new Cache($adapter);
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException() {
        return array(
            array('ttl', 0, '\Desarrolla2\Cache\Exception\CacheException'),
            array('file', 100, '\Desarrolla2\Cache\Exception\CacheException'),
        );
    }
}
