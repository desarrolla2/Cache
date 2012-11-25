<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of Memcached
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : Memcached.php , UTF-8
 * @date : Sep 4, 2012 , 1:00:33 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\MemcachedCacheException;

class Memcached extends AbstractAdapter implements AdapterInterface
{
    protected $memcached;

    public function __construct($host, $port)
    {
        throw new MemcachedCacheException('this adapter is not ready yet');
    }

    public function delete($key)
    {
        
    }

    public function get($key)
    {
        
    }

    public function has($key)
    {
        
    }

    public function set($key, $value, $ttl = null)
    {
        
    }

    public function setOption($key, $value)
    {
        
    }

}
