<?php
/**
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

use Cache\IntegrationTests\SimpleCacheTest;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * AbstractCacheTest
 */
abstract class AbstractCacheTest extends SimpleCacheTest
{
    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return [
            ['ttl', 100],
            ['prefix', 'test']
        ];
    }

    /**
     * @dataProvider dataProviderForOptions
     *
     * @param string $key
     * @param mixed  $value
     */
    public function testWithOption($key, $value)
    {
        $cache = $this->cache->withOption($key, $value);
        $this->assertEquals($value, $cache->getOption($key));

        // Check immutability
        $this->assertNotSame($this->cache, $cache);
        $this->assertNotEquals($value, $this->cache->getOption($key));
    }

    public function testWithOptions()
    {
        $data = $this->dataProviderForOptions();
        $options = array_combine(array_column($data, 0), array_column($data, 1));

        $cache = $this->cache->withOptions($options);

        foreach ($options as $key => $value) {
            $this->assertEquals($value, $cache->getOption($key));
        }

        // Check immutability
        $this->assertNotSame($this->cache, $cache);

        foreach ($options as $key => $value) {
            $this->assertNotEquals($value, $this->cache->getOption($key));
        }
    }


    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return [
            ['ttl', 0, InvalidArgumentException::class],
            ['foo', 'bar', InvalidArgumentException::class]
        ];
    }

    /**
     * @dataProvider dataProviderForOptionsException
     *
     * @param string   $key
     * @param mixed    $value
     * @param string   $expectedException
     */
    public function testWithOptionException($key, $value, $expectedException)
    {
        $this->expectException($expectedException);
        $this->createSimpleCache()->withOption($key, $value);
    }
}
