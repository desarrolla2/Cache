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

/**
 * Memory
 */
class Memory extends AbstractAdapter
{
    /**
     * @var int
     */
    protected $limit = false;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * {@inheritdoc}
     */
    public function del($key)
    {
        unset($this->cache[$this->getKey($key)]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if ($this->has($key)) {
            $tKey = $this->getKey($key);

            return $this->unPack($this->cache[$tKey]['value']);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $tKey = $this->getKey($key);
        if (isset($this->cache[$tKey])) {
            $data = $this->cache[$tKey];
            if (time() < $data['ttl']) {
                return true;
            }
            $this->del($key);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if ($this->limit && count($this->cache) > $this->limit) {
            array_shift($this->cache);
        }
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->cache[$this->getKey($key)] = [
            'value' => serialize($value),
            'ttl' => (int) $ttl + time(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'limit':
                $value = (int) $value;
                $this->limit = $value;

                return true;
        }

        return parent::setOption($key, $value);
    }
}
