<?php

/**
 * This file is part of the Cache project.
 *
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es>
 *
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use \Memcache as BaseMemCache;

/**
 *
 * Description of Mencache
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file   : MemCache.php , UTF-8
 * @date   : Jun 30, 2013 , 4:55:03 PM
 */
class MemCache extends AbstractAdapter
{

    /**
     *
     * @var \Memcache
     */
    protected $server;

    public function __construct()
    {
        $this->server = new BaseMemcache();
        //$this->server->addServer('localhost', 11211);
    }

    /**
     *
     * @param string $host
     * @param string $port
     */
    public function addServer($host, $port)
    {
        $this->server->addServer($host, $port);
    }

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        $tKey = $this->getKey($key);
        $this->server->delete($tKey);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $tKey = $this->getKey($key);
        $data = $this->server->get($tKey);

        return $this->unserialize($data);
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $tKey = $this->getKey($key);
        if ($this->server->get($tKey)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $tKey   = $this->getKey($key);
        $_value = $this->serialize($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->server->set($tKey, $_value, time() + $ttl);
    }
}
