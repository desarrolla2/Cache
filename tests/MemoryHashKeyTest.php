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

use Desarrolla2\Cache\KeyMaker\HashKeyMaker;
use Desarrolla2\Cache\Memory as MemoryCache;

/**
 * Memory with HashKeyMaker.
 * Doesn't adhere to PSR-16 keys.
 */
class MemoryHashKeyTest extends AbstractCacheTest
{
    public function createSimpleCache()
    {
        return (new MemoryCache())->withKeyMaker(new HashKeyMaker());
    }

    /**
     * Data provider for invalid keys.
     *
     * @return array
     */
    public static function invalidKeys()
    {
        return [
            [null],
            [new \stdClass()],
            [['array']],
        ];
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
