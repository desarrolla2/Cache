<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 * Copyright (c) Janos vajda
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel Gonz�lez <daniel@desarrolla2.com>
 * @author Janos Vajda <janos.vajda@customerfocus.com>
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\CacheException;

/**
 * WinCache
 */
class WinCache extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    public function del($key)
    {
        wincache_ucache_delete($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->getValueFromCache($key);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $value = $this->getValueFromCache($key);
        if (is_null($value)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        wincache_ucache_set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        return true;
    }

    protected function getValueFromCache($key)
    {
        return wincache_ucache_get($key);
    }

}
