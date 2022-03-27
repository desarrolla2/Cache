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

use Desarrolla2\Cache\NotCache as NotCache;
use PHPUnit\Framework\TestCase;

/**
 * NotCacheTest
 */
class NotCacheTest extends TestCase
{
    /**
     * @var \Desarrolla2\Cache\NotCache
     */
    protected $cache;

    public function setUp(): void
    {
        $this->cache = new NotCache();
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(),
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHas()
    {
        $this->cache->set('key', 'value');
        $this->assertFalse($this->cache->has('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGet()
    {
        $this->cache->set('key', 'value');
        $this->assertFalse($this->cache->get('key', false));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSet()
    {
        $this->assertFalse($this->cache->set('key', 'value'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete()
    {
        $this->assertTrue($this->cache->delete('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWithOption()
    {
        $cache = $this->cache->withOption('ttl', 3600);
        $this->assertSame(3600, $cache->getOption('ttl'));

        $this->assertNotSame($this->cache, $cache);
    }
}
