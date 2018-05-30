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
use Memcached as BaseMemcached;

/**
 * Memcached
 */
class Memcached extends AbstractCache
{
    /**
     * @var BaseMemcached
     */
    protected $server;

    /**
     * @param BaseMemcached|null $server
     */
    public function __construct(BaseMemcached $server = null)
    {
        if (!$server) {
            $server = new BaseMemcached();
            $server->addServer('localhost', 11211);
        }

        $this->server = $server;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->server->delete($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $data = $this->server->get($this->getKey($key));

        if ($data === false) {
            return $default;
        }

        return $this->unpack($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');

        $cacheKeys = array_map([$this, 'getKey'], $keys);

        $items = $this->server->getMulti($cacheKeys);

        $result = array_map(function($item) {
            return $this->unpack($item);
        }, $items);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->server->get($this->getKey($key)) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $packed = $this->pack($value, $ttl);
        $ttlTime = static::time() + ($ttl ?: $this->ttl);

        return $this->server->set($this->getKey($key), $packed, $ttlTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $cacheKeys = array_map([$this, 'getKey'], array_keys($values));

        $packed = array_map(function($value) {
            $this->pack($value);
        }, $values);

        $ttlTime = static::time() + ($ttl ?: $this->ttl);

        return $this->server->setMulti(array_combine($cacheKeys, $packed), $ttlTime);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->server->flush();
    }
}
