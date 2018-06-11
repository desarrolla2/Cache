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
    public function createSimpleCache()
    {
        if (!class_exists('Predis\Client')) {
            return $this->markTestSkipped('The predis library is not available');
        }

        try {
            $predis = new Client();
            $predis->connect();
        } catch (ConnectionException $e) {
            return $this->markTestSkipped($e->getMessage());
        }

        return new PredisCache($predis);
    }
}
