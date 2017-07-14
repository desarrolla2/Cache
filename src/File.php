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

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * File
 */
class File extends AbstractCache
{
    use PackTtlTrait;
    
    const CACHE_FILE_PREFIX = '__';

    const CACHE_FILE_SUBFIX = '.php.cache';

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
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

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $tKey = $this->getKey($key);
        $cacheFile = $this->getFileName($tKey);
        $this->deleteFile($cacheFile);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->getValueFromCache($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return !is_null($this->getValueFromCache($key));
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFileName($key);
        if (!$ttl) {
            $ttl = $this->ttl;
        }

        $item = $this->pack($value, $ttl);

        if (!file_put_contents($cacheFile, $item)) {
            throw new CacheException(sprintf('Error saving data with the key "%s" to the cache file.', $key));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
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

    protected function createCacheDirectory($path)
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new CacheException($path.' is not writable');
            }
        }

        if (!is_writable($path)) {
            throw new CacheException($path.' is not writable');
        }
    }

    protected function deleteFile($cacheFile)
    {
        if (is_file($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
    }

    protected function getFileName($key)
    {
        return $this->cacheDir.
        DIRECTORY_SEPARATOR.
        self::CACHE_FILE_PREFIX.
        $this->getKey($key).
        self::CACHE_FILE_SUBFIX;
    }

    protected function getValueFromCache($key, $default = null)
    {
        $path = $this->getFileName($key);

        if (!file_exists($path)) {
            return $default;
        }
        try {
            $data = $this->unPack(file_get_contents($path));
        } catch( Exception $e ){
            return $default;
        }
        
        return $data;
    }

    protected function validateDataFromCache($data)
    {
        if (!is_array($data)) {
            return false;
        }
        foreach (['value', 'ttl'] as $missing) {
            if (!array_key_exists($missing, $data)) {
                return false;
            }
        }

        return true;
    }

    protected function ttlHasExpired($ttl)
    {
        return (time() > $ttl);
    }
}
