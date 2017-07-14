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
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Memcached as BaseMemcached;

/**
 * Memcached
 */
class Memcached extends AbstractCache
{
    use PackTtlTrait;
    /**
     * @var BaseMemcached
     */
    protected $server;

    /**
     * @param BaseMemcached|null $server
     */
    public function __construct(BaseMemcached $server = null)
    {
        if ($server) {
            $this->server = $server;

            return;
        }
        $this->server = new BaseMemcached();
        $this->server->addServer('localhost', 11211);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->server->delete($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $data = $this->server->get($this->getKey($key));
        if (!$data) {
            return $default;
        }

        return $this->unPack($data);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $data = $this->server->get($this->getKey($key));
        if (!$data) {
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
        $this->server->set($this->getKey($key), $this->pack($value, $ttl), false, time() + $ttl);
    }
}
