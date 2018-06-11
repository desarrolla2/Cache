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

use Desarrolla2\Cache\Mongo as MongoCache;

/**
 * MongoTest
 */
class MongoTest extends AbstractCacheTest
{
    public function createSimpleCache()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped(
                'The mongo extension is not available.'
            );
        }

        $client = new \MongoClient($this->config['mongo']['dsn']);
        $database = $client->selectDB($this->config['mongo']['database']);

        return new MongoCache($database);
    }

    /**
     * No sleep
     */
    protected static function sleep($seconds)
    {
        return;
    }

    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return [
            ['ttl', 100],
        ];
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
