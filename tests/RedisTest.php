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

use Desarrolla2\Cache\Redis as RedisCache;
use Redis as PhpRedis;

/**
 * RedisTest
 */
class RedisTest extends AbstractCacheTest
{
    /**
     * @var PhpRedis
     */
    protected $client;

    public function setUp()
    {
        if (!\extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not available.');
        }

        $client = new PhpRedis();

        $success = @$client->connect(CACHE_TESTS_REDIS_HOST, CACHE_TESTS_REDIS_PORT);
        if (!$success) {
            $this->markTestSkipped('Cannot connect to Redis.');
        }

        $this->client = $client;

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        if ($this->client->isConnected()) {
            $this->client->close();
        }
    }

    public function createSimpleCache()
    {
        return new RedisCache($this->client);
    }
}
