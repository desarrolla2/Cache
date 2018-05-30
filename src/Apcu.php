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
use Desarrolla2\Cache\Packer\NopPacker;
use Desarrolla2\Cache\Packer\PackerInterface;

/**
 * Apcu
 */
class Apcu extends AbstractCache
{
    /**
     * Get the packer
     *
     * @return PackerInterface
     */
    protected function getPacker()
    {
        if (!isset($this->packer)) {
            $this->packer = new NopPacker();
        }

        return $this->packer;
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return apcu_delete($this->getKey($key));
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
        return apcu_exists($key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        return apcu_store($this->getKey($key), $this->pack($value), $ttl ?: $this->ttl);
    }
}
