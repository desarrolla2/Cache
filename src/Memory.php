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
    use PackTtlTrait;
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
    public function delete($key)
    {
        unset($this->cache[$this->getKey($key)]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $tKey = $this->getKey($key);

            return $this->unPack($this->cache[$tKey]);
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
            try {
                $this->unPack($this->cache[$tKey]);
            } catch( UnexpectedValueException $e ){
                return false;
            }  catch( CacheExpiredException $e ){
                return false;
            }
            return true;
        }
        
        $this->delete($key);
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
        $this->cache[$this->getKey($key)] = $this->pack($value, $ttl);
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
