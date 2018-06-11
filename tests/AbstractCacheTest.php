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

use Cache\IntegrationTests\SimpleCacheTest;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * AbstractCacheTest
 */
abstract class AbstractCacheTest extends SimpleCacheTest
{
    /**
     * @var \Desarrolla2\Cache\Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $config = [];

    public function setUp()
    {
        $configurationFile = __DIR__.'/config.json';

        if (!is_file($configurationFile)) {
            throw new \Exception(' Configuration file not found in "'.$configurationFile.'" ');
        }
        $this->config = json_decode(file_get_contents($configurationFile), true);

        parent::setUp();
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
     * @dataProvider dataProviderForOptions
     *
     * @param string $key
     * @param mixed  $value
     */
    public function testWithOption($key, $value)
    {
        $base = $this->createSimpleCache();
        $cache = $base->withOption($key, $value);
        $this->assertEquals($value, $cache->getOption($key));

        // Check immutability
        $this->assertNotSame($base, $cache);
        $this->assertNotEquals($value, $base->getOption($key));
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return [
            ['ttl', 0, InvalidArgumentException::class],
            ['file', 100, InvalidArgumentException::class]
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
