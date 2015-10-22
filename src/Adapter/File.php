<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\CacheException;

/**
 * File
 */
class File extends AbstractAdapter
{
    const CACHE_FILE_PREFIX = '__';
    const CACHE_FILE_SUBFIX = '.php.cache';

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param  null $cacheDir
     *
     * @throws CacheException
     */
    public function __construct($cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()).'/cache';
        }

        $this->cacheDir = (string) $cacheDir;
        
        $this->createCacheDirectory($cacheDir);
    }

    protected function createCacheDirectory($path)
    {
        if (! is_dir($path)) {
            if (! mkdir($path, 0777, true)) {
                throw new CacheException($path.' is not writable');
            }
        }

        if (! is_writable($path)) {
            throw new CacheException($path.' is not writable');
        }
    }

    /**
     * Delete a value from the cache
     *
     * @param string $key
     */
    public function delete($key)
    {
        $tKey = $this->getKey($key);
        $cacheFile = $this->getCacheFile($tKey);
        $this->deleteFile($cacheFile);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $data = $this->getCacheData($key);

        if (isset($data['value'])) {
            return $data['value'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return ! is_null($this->getData($key));
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $tKey = $this->getKey($key);
        $cacheFile = $this->getCacheFile($tKey);
        $tValue = $this->serialize($value);
        if (!($ttl)) {
            $ttl = $this->ttl;
        }
        $item = $this->serialize(
            [
                'value' => $tValue,
                'ttl' => (int)$ttl + time(),
            ]
        );
        if (!file_put_contents($cacheFile, $item)) {
            throw new CacheException('Error saving data with the key "'.$key.'" to the cache file.');
        }
    }

    /**
     * {@inheritdoc }
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int)$value;
                if ($value < 1) {
                    throw new CacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            default:
                throw new CacheException('option not valid '.$key);
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
            $cacheFile = $this->cacheDir.
                DIRECTORY_SEPARATOR.
                $fileName;
            $this->deleteFile($cacheFile);
        }
    }

    /**
     * Delete file
     *
     * @param  string $cacheFile
     *
     * @return bool
     */
    protected function deleteFile($cacheFile)
    {
        if (is_file($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
    }

    /**
     * Get the specified cache file path
     */
    protected function getCacheFile($fileName)
    {
        return $this->cacheDir.
            DIRECTORY_SEPARATOR.
            self::CACHE_FILE_PREFIX.
            $fileName.
            self::CACHE_FILE_SUBFIX;
    }

    /**
     * Load the contents of a file at a given path and unserialize the contents
     * @param  string $path The path of the file to load
     * @return array        Unserialized file contents
     *
     * @throws Desarrolla2\Cache\Exception\CacheException
     */
    protected function loadFile($path)
    {
        if (! file_exists($path)) {
            return;
        }

        $data = unserialize(file_get_contents($path));

        if (! $data) {
            throw new CacheException("Unable to load cache file at {$path}");
        }

        return $data;
    }

    /**
     * Get a standardised data structure irrespective of failure in
     * order to better determine hits/misses etc when using has()
     * @param  string $key Cache key to attempt to load
     * @return array       Standardised array format with keys 'value' and 'ttl'
     */
    protected function getCacheData($key)
    {
        $path = $this->getCacheFile($this->getKey($key));

        $data = $this->loadFile($path);

        if (! $data) {
            return ['value' => null, 'ttl' => null];
        }

        // Determine if the structure of our data is correct
        // Leaving this throwing exceptions to prevent BC breaks
        $this->validateCacheData($data, $path);

        // Expire old cache values
        if ($this->ttlHasExpired($data['ttl'])) {
            $this->delete($key);

            return ['value' => null, 'ttl' => null];
        }

        return [
            'value' => unserialize($data['value']),
            'ttl'   => $data['ttl']
        ];
    }

    /**
     * Determine if a given timestamp is in the past
     * @param  int $ttl Unix timestamp denoting the desired expiry time
     * @return [type]      [description]
     */
    private function ttlHasExpired($ttl)
    {
        return (time() > $ttl);
    }

    /**
     * [validateCacheData description]
     * @param  [type] $data [description]
     * @param  [type] $path [description]
     * @return void
     *
     * @throws Desarrolla2\Cache\Exception\CacheException
     */
    protected function validateCacheData($data, $path = null)
    {
        foreach (['value', 'ttl'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw new CacheException("{$key} missing from cache file {$path}");
            }
        }
    }

    /**
     * Get data value from file cache
     * @param  string $key
     * @return mixed
     */
    protected function getData($key)
    {
        return $this->getCacheData($key)['value'];
    }
}
