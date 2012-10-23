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
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\FileCacheException;

class File extends AbstractAdapter implements AdapterInterface
{

    /**
     * @var string
     */
    protected $cacheDir = '/tmp';

    /**
     * {@inheritdoc } 
     */
    public function delete($key)
    {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    /**
     * {@inheritdoc } 
     */
    public function get($key)
    {
        if ($data = $this->getData($key)) {
            return $data['value'];
        }
        return false;
    }

    /**
     * {@inheritdoc } 
     */
    public function has($key)
    {
        if ($this->getData($key)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc } 
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getCacheFile($key);
        if (is_null($ttl)) {
            $ttl = $this->ttl;
        }
        $item = array(
            'value' => $value,
            'ttl'   => $ttl,
            'time'  => time(),
        );
        if (!file_put_contents($cacheFile, serialize($item))) {
            throw new FileCacheException('Error saving data with the key "' . $key . '" to the cache file.');
        }
    }

    /**
     * {@inheritdoc } 
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new FileCacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            case 'cacheDir':
                $this->cacheDir = (string) $value;
                break;
            default :
                throw new FileCacheException('option not valid ' . $key);
        }
        return true;
    }

    /**
     * Get the specified cache file
     */
    protected function getCacheFile($key)
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.php.cache';
    }

    /**
     * Get data value from file cache
     * 
     * @param type $key
     * @return boolean
     * @throws FileCacheException
     */
    protected function getData($key)
    {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile)) {
            if (!$data = unserialize(file_get_contents($cacheFile))) {
                throw new FileCacheException('Error with the key "' . $key . '" in cache file ' . $cacheFile);
            }
            if (!array_key_exists('value', $data)) {
                throw new FileCacheException('Error with the key "' . $key . '" in cache file ' . $cacheFile . ', value not exist');
            }
            if (!array_key_exists('ttl', $data)) {
                throw new FileCacheException('Error with the key "' . $key . '" in cache file ' . $cacheFile . ', ttl not exist');
            }
            if (!array_key_exists('time', $data)) {
                throw new FileCacheException('Error with the key "' . $key . '" in cache file ' . $cacheFile . ', time not exist');
            }
            if (time() > $data['ttl'] + $data['time']) {
                return false;
            }
            return $data;
        }
    }

}
