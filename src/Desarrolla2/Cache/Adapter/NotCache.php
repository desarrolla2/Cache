<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of NoCache
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : NoCache.php , UTF-8
 * @date : Sep 4, 2012 , 1:00:01 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;

class NotCache extends AbstractAdapter implements AdapterInterface
{

    /**
     * {@inheritdoc } 
     */
    public function delete($key)
    {
        return;
    }

    /**
     * {@inheritdoc } 
     */
    public function get($key)
    {
        return false;
    }

    /**
     * {@inheritdoc } 
     */
    public function has($key)
    {
        return false;
    }

    /**
     * {@inheritdoc } 
     */
    public function set($key, $value, $ttl = null)
    {
        return false;
    }

    public function setOption($key, $value)
    {
         return;
    }

}
