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

use Desarrolla2\Cache\Memory as MemoryCache;

/**
 * MemoryTest
 */
class MemoryTest extends AbstractCacheTest
{
    public function createSimpleCache()
    {
        return new MemoryCache();
    }

    public function tearDown(): void
    {
        // No need to clear cache, as the adapters don't persist between tests.
    }

    public function testExceededLimit()
    {
        $cache = $this->createSimpleCache()->withOption('limit', 1);

        $cache->set('foo', 1);
        $this->assertTrue($cache->has('foo'));

        $cache->set('bar', 1);
        $this->assertFalse($cache->has('foo'));
        $this->assertTrue($cache->has('bar'));
    }
}
