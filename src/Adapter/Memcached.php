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

use Memcached as BaseMemcached;

/**
 * Memcached
 */
class Memcached extends AbstractAdapter
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
    public function del($key)
    {
        $this->server->delete($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = $this->server->get($this->getKey($key));
        if (!$data) {
            return;
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
        $this->server->set($this->getKey($key), $this->pack($value), time() + $ttl);
    }
}
