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

use Desarrolla2\Cache\Predis as PredisCache;
use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * PredisTest
 */
class PredisTest extends AbstractCacheTest
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        if (!class_exists('Predis\Client')) {
            return $this->markTestSkipped('The predis library is not available');
        }

        try {
            $this->client = new Client(CACHE_TESTS_PREDIS_DSN, ['exceptions' => false]);
            $this->client->connect();
        } catch (ConnectionException $e) {
            return $this->markTestSkipped($e->getMessage());
        }

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->client->disconnect();
    }

    public function createSimpleCache()
    {
        return new PredisCache($this->client);
    }
}
