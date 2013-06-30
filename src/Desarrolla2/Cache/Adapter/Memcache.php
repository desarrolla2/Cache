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
        $_key = $this->getKey($key);
        return $this->server->delete($_key);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $_key = $this->getKey($key);
        $data = $this->server->get($_key);
        return $this->unserialize($data);
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $_key = $this->getKey($key);
        if ($this->server->get($_key)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $_key = $this->getKey($key);
        $_value = $this->serialize($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->server->set($_key, $_value, time() + $ttl);
    }

}