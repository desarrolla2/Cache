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
use Desarrolla2\Cache\Exception\AdapterNotSetException;

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
        $this->getAdapter()->delete($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function get($key)
    {
        return $this->getAdapter()->get($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function getAdapter()
    {
        if (!$this->adapter) {
            throw new AdapterNotSetException('Required Adapter');
        }
        return $this->adapter;
    }

    /**
     * {@inheritdoc } 
     */
    public function has($key)
    {
        return $this->getAdapter()->has($key);
    }

    /**
     * {@inheritdoc } 
     */
    public function set($key, $value, $ttl = null)
    {
        $this->getAdapter()->set($key, $value, $ttl);
    }

    /**
     * {@inheritdoc } 
     */
    public function setAdapter(\Desarrolla2\Cache\Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * {@inheritdoc } 
     */
    public function setOption($key, $value)
    {
        $this->adapter->setOption($key, $value);
    }
    
    

}
