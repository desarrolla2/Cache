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

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\FileCacheException;

class File extends AbstractAdapter
{

    const CACHE_FILE_PREFIX = '__';
    const CACHE_FILE_SUBFIX = '.php.cache';

    /**
     * @var string
     */
    protected $cacheDir;

    public function __construct($cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir());
        }
        $this->cacheDir = (string) $cacheDir;
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0777, true)) {
                throw new FileCacheException($this->cacheDir . ' is not writable');
            }
        }
        if (!is_writable($this->cacheDir)) {
            throw new FileCacheException($this->cacheDir . ' is not writable');
        }
    }

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        $_key = $this->getKey($key);
        $cacheFile = $this->getCacheFile($_key);
        $this->deleteFile($cacheFile);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        if ($data = $this->getData($key)) {
            return $data;
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
        $_key = $this->getKey($key);
        $cacheFile = $this->getCacheFile($_key);
        $_value = $this->serialize($value);
        if (!($ttl)) {
            $ttl = $this->ttl;
        }
        $item = $this->serialize(array(
            'value' => $_value,
            'ttl' => (int) $ttl + time(),
        ));
        if (!file_put_contents($cacheFile, $item)) {
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
            default :
                throw new FileCacheException('option not valid ' . $key);
        }

        return true;
    }

    /**
     * {@inheritdoc }
     */
    public function clearCache()
    {
        throw new Exception('not ready yet');
    }

    /**
     * {@inheritdoc }
     */
    public function dropCache()
    {
        foreach (scandir($this->cacheDir) as $fileName) {
            $cacheFile = $this->cacheDir .
                    DIRECTORY_SEPARATOR .
                    $fileName;
            $this->deleteFile($cacheFile);
        }
    }

    /**
     * Delete file
     * 
     * @param type $cacheFile
     */
    protected function deleteFile($cacheFile)
    {
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    /**
     * Get the specified cache file
     */
    protected function getCacheFile($fileName)
    {
        return $this->cacheDir .
                DIRECTORY_SEPARATOR .
                self::CACHE_FILE_PREFIX .
                $fileName .
                self::CACHE_FILE_SUBFIX;
    }

    /**
     * Get data value from file cache
     *
     * @param  type               $key
     * @return boolean
     * @throws FileCacheException
     */
    protected function getData($key)
    {
        $_key = $this->getKey($key);
        $cacheFile = $this->getCacheFile($_key);
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
            if (time() > $data['ttl']) {
                $this->delete($key);
                return false;
            }
            return $this->unserialize($data['value']);
        }
        return false;
    }

}
