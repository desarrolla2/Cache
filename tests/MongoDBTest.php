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

use Desarrolla2\Cache\MongoDB as MongoDBCache;
use MongoDB\Client;

/**
 * MongoDBTest
 */
class MongoDBTest extends AbstractCacheTest
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * Use one client per test, as the MongoDB extension leaves connections open
     */
    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('mongodb')) {
            return;
        }

        self::$client = new Client(CACHE_TESTS_MONGO_DSN);
        self::$client->listDatabases(); // Fail if unable to connect
    }

    public function createSimpleCache()
    {
        if (!isset(self::$client)) {
            $this->markTestSkipped('The mongodb extension is not available');
        }

        $collection = self::$client->selectCollection(CACHE_TESTS_MONGO_DATABASE, 'cache');

        return (new MongoDBCache($collection))
            ->withOption('initialize', false);
    }
}
