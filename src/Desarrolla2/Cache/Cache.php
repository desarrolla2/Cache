<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of Cache
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : Cache.php , UTF-8
 * @date : Sep 4, 2012 , 12:45:14 AM
 */

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\CacheInterface;

class Cache implements CacheInterface
{

    /**
     *
     * @var Adapter\AdapterInterface 
     */
    protected $adapter;

    /**
     * {@inheritdoc } 
     */
    public function delete($key)
    {
        $this->adapter->delete($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function get($key)
    {
        return $this->get->get($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc } 
     */
    public function has($key)
    {
        return $this->get->has($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function set($key, $value, $ttl = null)
    {
        $this->adapter->set($key, $value, $ttl);
    }

    /**
     * {@inheritdoc } 
     */
    public function setAdapter(Desarrolla2\Cache\Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

}
