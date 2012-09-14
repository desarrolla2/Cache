<?php

/**
 * This file is part of the D2Cache proyect.
 * 
 * Description of File
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : File.php , UTF-8
 * @date : Sep 4, 2012 , 1:00:09 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Exception\FileCacheException;

class File implements AdapterInterface
{

    /**
     * @var int
     */
    protected $ttl;

    public function __construct()
    {
        $cacheFile = $this->getCacheFile($key);
        if (!file_put_contents($cacheFile, serialize($data))) {
            throw new FileCacheException('Error saving data with the key ' . $key . ' to the cache file.');
        }
    }

    /**
     * {@inheritdoc } 
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            $cacheFile = $this->getCacheFile($key);
            if (!unlink($cacheFile)) {
                throw new FileCacheException('Error deleting the file cache with key ' . $key);
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc } 
     */
    public function get($key)
    {
        if ($this->has($key)) {
            $cacheFile = $this->getCacheFile($key);
            if (!$data = unserialize(file_get_contents($cacheFile))) {
                throw new FileCacheException('Error reading data with the key ' . $key . ' from the cache file.');
            }
            return $data;
        }
        return null;
    }

    /**
     * {@inheritdoc } 
     */
    public function has($key)
    {
        $cacheFile = $this->getCacheFile($key);
        return file_exists($cacheFile);
    }

    /**
     * {@inheritdoc } 
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getCacheFile($key);
        if (!file_put_contents($cacheFile, serialize($data))) {
            throw new FileCacheException('Error saving data with the key ' . $key . ' to the cache file.');
        }
        return $this;
    }

    /**
     * Get the specified cache file
     */
    protected function getCacheFile($key)
    {
        return $this->_cacheDir . DIRECTORY_SEPARATOR . strtolower($key) . '.cache';
    }

    /**
     * {@inheritdoc } 
     */
    public function setDefaultTtl($ttl)
    {
        
    }

    /**
     * {@inheritdoc } 
     */
    public function setOption($key, $value)
    {
        
    }

}
