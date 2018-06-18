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
 * @author Arnold Daniels <arnold@jasny.net>
 */

declare(strict_types=1);

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;

/**
 * Memory
 */
class Memory extends AbstractCache
{
    /**
     * Limit the amount of entries
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
     * Create the default packer for this cache implementation.
     * {@internal NopPacker might fail PSR-16, as cached objects would change}
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Make a clone of this object.
     * Set by cache reference, thus using the same pool.
     *
     * @return static
     */
    protected function cloneSelf(): AbstractCache
    {
        $clone = clone $this;

        $clone->cache =& $this->cache;
        $clone->cacheTtl =& $this->cacheTtl;

        return $clone;
    }

    /**
     * Set the max number of items
     *
     * @param int $limit
     */
    protected function setLimitOption($limit)
    {
        $this->limit = (int)$limit ?: PHP_INT_MAX;
    }

    /**
     * Get the max number of items
     *
     * @return int
     */
    protected function getLimitOption()
    {
        return $this->limit;
    }


    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $id = $this->keyToId($key);

        return $this->unpack($this->cache[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $id = $this->keyToId($key);

        if (!isset($this->cacheTtl[$id])) {
            return false;
        }

        if ($this->cacheTtl[$id] <= time()) {
            unset($this->cache[$id], $this->cacheTtl[$id]);
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if (count($this->cache) >= $this->limit) {
            $deleteKey = key($this->cache);
            unset($this->cache[$deleteKey], $this->cacheTtl[$deleteKey]);
        }

        $id = $this->keyToId($key);

        $this->cache[$id] = $this->pack($value);
        $this->cacheTtl[$id] = $this->ttlToTimestamp($ttl) ?? PHP_INT_MAX;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $id = $this->keyToId($key);
        unset($this->cache[$id], $this->cacheTtl[$id]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->cache = [];
        $this->cacheTtl = [];

        return true;
    }
}
