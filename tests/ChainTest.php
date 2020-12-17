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

use Desarrolla2\Cache\Chain as CacheChain;
use Desarrolla2\Cache\Memory as MemoryCache;

/**
 * ChainTest
 */
class ChainTest extends AbstractCacheTest
{
    public function createSimpleCache()
    {
        $adapters = [new MemoryCache()]; // For the general PSR-16 tests, we don't need more than 1 adapter

        return new CacheChain($adapters);
    }


    public function tearDown(): void
    {
        // No need to clear cache, as the adapters don't persist between tests.
    }


    public function testChainSet()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('set')->with("foo", "bar", 300);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('set')->with("foo", "bar", 300);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $cache->set("foo", "bar", 300);
    }

    public function testChainSetMultiple()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('setMultiple')->with(["foo" => 1, "bar" => 2], 300);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('setMultiple')->with(["foo" => 1, "bar" => 2], 300);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $cache->setMultiple(["foo" => 1, "bar" => 2], 300);
    }


    public function testChainGetFirst()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('get')->with("foo")->willReturn("bar");

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->never())->method('get');

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertEquals("bar", $cache->get("foo", 42));
    }

    public function testChainGetSecond()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('get')->with("foo")->willReturn(null);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('get')->with("foo")->willReturn("car");

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertEquals("car", $cache->get("foo", 42));
    }

    public function testChainGetNone()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('get')->with("foo")->willReturn(null);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('get')->with("foo")->willReturn(null);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertEquals(42, $cache->get("foo", 42));
    }


    public function testChainGetMultipleFirst()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('getMultiple')->with(["foo", "bar"])
            ->willReturn(["foo" => 1, "bar" => 2]);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->never())->method('getMultiple');

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertEquals(["foo" => 1, "bar" => 2], $cache->getMultiple(["foo", "bar"]));
    }

    public function testChainGetMultipleMixed()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('getMultiple')
            ->with($this->equalTo(["foo", "bar", "wux", "lot"]))
            ->willReturn(["foo" => null, "bar" => 2, "wux" => null, "lot" => null]);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('getMultiple')
            ->with($this->equalTo(["foo", "wux", "lot"]))
            ->willReturn(["foo" => 11, "wux" => 15, "lot" => null]);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $expected = ["foo" => 11, "bar" => 2, "wux" => 15, "lot" => 42];
        $this->assertEquals($expected, $cache->getMultiple(["foo", "bar", "wux", "lot"], 42));
    }
    

    public function testChainHasFirst()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('has')->with("foo")->willReturn(true);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->never())->method('has');

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertTrue($cache->has("foo"));
    }

    public function testChainHasSecond()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('has')->with("foo")->willReturn(false);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('has')->with("foo")->willReturn(true);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertTrue($cache->has("foo"));
    }

    public function testChainHasNone()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('has')->with("foo")->willReturn(false);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('has')->with("foo")->willReturn(false);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $this->assertFalse($cache->has("foo"));
    }


    public function testChainDelete()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('delete')->with("foo");

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('delete')->with("foo");

        $cache = new CacheChain([$adapter1, $adapter2]);

        $cache->delete("foo");
    }

    public function testChainDeleteMultiple()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('deleteMultiple')->with(["foo", "bar"]);

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('deleteMultiple')->with(["foo", "bar"]);

        $cache = new CacheChain([$adapter1, $adapter2]);

        $cache->deleteMultiple(["foo", "bar"]);
    }

    public function testChainClear()
    {
        $adapter1 = $this->createMock(MemoryCache::class);
        $adapter1->expects($this->once())->method('clear');

        $adapter2 = $this->createMock(MemoryCache::class);
        $adapter2->expects($this->once())->method('clear');

        $cache = new CacheChain([$adapter1, $adapter2]);

        $cache->clear();
    }
}
