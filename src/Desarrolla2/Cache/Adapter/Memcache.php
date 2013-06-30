<?php

/**
 * This file is part of the Cache proyect.
 * 
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use \Memcache as BaseMemcache;

/**
 * 
 * Description of Mencache
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : Memcache.php , UTF-8
 * @date : Jun 30, 2013 , 4:55:03 PM
 */
class Memcache extends AbstractAdapter
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
        return $this->server->delete($this->getKey($key));
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $key = $this->getKey($key);
        $data = $this->server->get($key);
        return unserialize($data);
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        if ($this->server->get($key)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $key = $this->getKey($key);
        $value = serialize($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->server->set($key, $value, time() + $ttl);
    }

}