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

use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\NopPacker;

/**
 * Apcu
 */
class Apcu extends AbstractCache
{
    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }


    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $ttlSeconds = $this->ttlToSeconds($ttl ?? $this->ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->delete($key);
        }

        return apcu_store($this->getKey($key), $this->pack($value), $ttlSeconds ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $packed = apcu_fetch($this->getKey($key), $success);

        if (!$success) {
            return $default;
        }

        return $this->unpack($packed);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return apcu_exists($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cacheKey = $this->getKey($key);

        return apcu_delete($cacheKey) || !apcu_exists($cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return apcu_clear_cache();
    }

}
