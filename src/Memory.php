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

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Exception\CacheExpiredException;
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Memory
 */
class Memory extends AbstractCache
{
    /**
     * @var int
     */
    protected $limit = PHP_INT_MAX;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var array
     */
    protected $cacheTtl = [];


    /**
     * Set the max number of items
     *
     * @param int $limit
     */
    public function setLimitOption($value)
    {
        $this->limit = (int)$value ?: PHP_INT_MAX;
    }

    /**
     * Get the max number of items
     *
     * @return int
     */
    public function getLimitOption()
    {
        return $this->limit;
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cacheKey = $this->getKey($key);
        unset($this->cache[$cacheKey], $this->cacheTtl[$cacheKey]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $cacheKey = $this->getKey($key);

        return $this->unpack($this->cache[$cacheKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $cacheKey = $this->getKey($key);

        if (!isset($this->cacheTtl[$cacheKey])) {
            return false;
        }

        if ($this->cacheTtl[$cacheKey] < self::time()) {
            unset($this->cache[$cacheKey], $this->cacheTtl[$cacheKey]);
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if (count($this->cache) > $this->limit) {
            array_shift($this->cache);
        }

        $cacheKey = $this->getKey($key);

        $this->cache[$cacheKey] = $this->pack($value);
        $this->cacheTtl[$cacheKey] = self::time() + ($ttl ?: $this->ttl);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->cache = [];
        $this->cacheTtl = [];
    }
}
