<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of AdapterInterface
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : AdapterInterface.php , UTF-8
 * @date : Sep 4, 2012 , 12:49:07 AM
 */

namespace Desarrolla2\Cache\Adapter;

interface AdapterInterface
{
    
    /**
     * Constructor
     */
    public function __construct();

    /**
     * Delete a value from the cache
     * 
     * @param string $key
     */
    public function delete($key);

    /**
     * Retrieve the value corresponding to a provided key
     *     
     * @param string $key
     */
    public function get($key);

    /**
     * Retrieve the if value corresponding to a provided key exist
     *
     * @param string $key
     */
    public function has($key);

    /**
     * * Add a value to the cache under a unique key
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     */
    public function set($key, $value, $ttl = null);

    /**
     * Set option for Adapter
     * 
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value);
}
