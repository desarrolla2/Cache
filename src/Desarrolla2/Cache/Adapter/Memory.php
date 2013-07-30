<?php

/**
 * This file is part of the Cache project.
 *
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AbstractAdapter;

/**
 *
 * Description of Memory
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 */
class Memory extends AbstractAdapter
{

    /**
     *
     * @var int
     */
    protected $limit = 100;

    /**
     *
     * @var array
     */
    protected $cache = array();

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        $_key = $this->getKey($key);
        unset($this->cache[$_key]);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        if ($this->has($key)) {
            $_key = $this->getKey($key);

            return $this->unserialize($this->cache[$_key]['value']);
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $_key = $this->getKey($key);
        if (isset($this->cache[$_key])) {
            $data = $this->cache[$_key];
            if (time() < $data['ttl']) {
                return true;
            } else {
                $this->delete($key);
            }
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        while (count($this->cache) >= $this->limit) {
            array_shift($this->cache);
        }
        $_key = $this->getKey($key);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->cache[$_key] = array(
            'value' => serialize($value),
            'ttl'   => $ttl + time(),
        );
    }

    /**
     * {@inheritdoc }
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'limit':
                $value = (int)$value;
                if ($value < 1) {
                    throw new MemoryCacheException('limit cant be lower than 1');
                }
                $this->limit = $value;

                return true;
        }

        return parent::setOption($key, $value);
    }
}
