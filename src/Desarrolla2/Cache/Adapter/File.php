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

    const CACHE_FILE_PREFIX = '__';
    const CACHE_FILE_SUBFIX = '.php.cache';

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * Last data loaded by getData
     * 
     * @var array 
     */
    protected $lastData = null;

    /**
     * Last key loaded by getData
     * 
     * @var string 
     */
    protected $lastKey = null;

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
        $cacheFile = $this->getCacheFileForKey($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        if ($key == $this->lastKey) {
            $this->lastKey = null;
            $this->lastData = null;
        }
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
        $cacheFile = $this->getCacheFileForKey($key);
        if (is_null($ttl)) {
            $ttl = $this->ttl;
        }
        $item = array(
            'value' => $value,
            'ttl' => $ttl,
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
     * 
     * @param string $key
     */
    protected function getCacheFileForKey($key)
    {
        return $this->getCacheFile(md5($key));
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
        if ($key == $this->lastKey) {
            return $this->lastData;
        }
        $cacheFile = $this->getCacheFileForKey($key);
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
            if (time() > $data['ttl'] + filemtime($cacheFile)) {
                return false;
            }

            $this->lastKey = $key;
            $this->lastData = $data['value'];
            return $data['value'];
        }
        return false;
    }

}
