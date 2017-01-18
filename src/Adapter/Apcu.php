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

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\CacheException;

/**
 * Apcu
 */
class Apcu extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    public function del($key)
    {
        apcu_delete($this->getKey($key));
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
        if (!apcu_store(
            $this->getKey($key),
            $this->pack(
                [
                    'value' => $value,
                    'ttl' => (int) $ttl + time(),
                ]
            ),
            $ttl
        )
        ) {
            throw new CacheException(sprintf('Error saving data with the key "%s" to the apcu cache.', $key));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new CacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            default:
                throw new CacheException('option not valid '.$key);
        }

        return true;
    }

    protected function getValueFromCache($key)
    {
        $data = $this->unPack(apcu_fetch($this->getKey($key)));
        if (!$this->validateDataFromCache($data, $key)) {
            $this->del($key);

            return;
        }
        if ($this->ttlHasExpired($data['ttl'])) {
            $this->del($key);

            return;
        }

        return $data['value'];
    }

    protected function validateDataFromCache($data)
    {
        if (!is_array($data)) {
            return false;
        }
        foreach (['value', 'ttl'] as $missing) {
            if (!array_key_exists($missing, $data)) {
                return false;
            }
        }

        return true;
    }

    protected function ttlHasExpired($ttl)
    {
        return (time() > $ttl);
    }
}
