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
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Packer\PhpPacker;

/**
 * Flat file.
 * TTL will place in it's own file.
 */
class FlatFile extends AbstractCache
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $filePrefix = '__';

    /**
     * @var string|null
     */
    protected $fileSuffix;

    /**
     * @param string $cacheDir
     * @throws CacheException
     */
    public function __construct($cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realcacheFile(sys_get_temp_dir()) . '/cache';
        }

        $this->cacheDir = (string)$cacheDir;
        $this->createCacheDirectory($cacheDir);
    }

    /**
     * Set the file prefix
     *
     * @param string $filePrefix
     */
    public function setFilePrefixOption($filePrefix)
    {
        $this->filePrefix = $filePrefix;
    }

    /**
     * Get the file prefix
     *
     * @return string
     */
    public function getFilePrefixOption()
    {
        return $this->filePrefix;
    }

    /**
     * Set the file extension
     *
     * @param string $fileSuffix
     */
    public function setFileSuffixOption($fileSuffix)
    {
        $this->fileSuffix = $fileSuffix;
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    public function getFileSuffixOption()
    {
        return isset($this->fileSuffix) ? $this->fileSuffix : ('.' . $this->getPacker()->getType());
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cacheFile = $this->getFileName($key);

        return $this->deleteFile($cacheFile);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $cacheFile = $this->getFileName($key);

        if (!$this->has($key)) {
            return $default;
        }

        return $this->read($cacheFile);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $ttlFile = $this->getFileName($key) . '.ttl';

        if (!file_exists($ttlFile)) {
            return false;
        }

        $ttl = $this->read($ttlFile);

        return self::time() < $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFileName($key);
        $ttlFile = $this->getFileName($key) . '.ttl';

        $packed = $this->pack($value);
        $packedTtl = $this->pack(self::time() + ($ttl ?: $this->ttl));

        if (!is_string($packed)) {
            throw new UnexpectedValueException("Packer must create a string for the data to be cached to file");
        }

        return file_put_contents($cacheFile, $packed) && file_put_contents($ttlFile, $packedTtl);
    }

    /**
     * Create the cache directory
     *
     * @param string $cacheFile
     */
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

    /**
     * Delete a cache file
     *
     * @param string $cacheFile
     * @return bool
     */
    protected function deleteFile($cacheFile)
    {
        return
            (!is_file($cacheFile) || unlink($cacheFile)) &&
            (!is_file($cacheFile . '.ttl') || unlink($cacheFile . '.ttl'));
    }

    /**
     * Create a filename based on the key
     *
     * @param $key
     * @return string
     */
    protected function getFileName($key)
    {
        return $this->cacheDir.
            DIRECTORY_SEPARATOR.
            $this->getFilePrefixOption().
            $this->getKey($key).
            $this->getFileSuffixOption();
    }

    /**
     * Read the cache file
     *
     * @param string $cacheFile
     * @return mixed
     */
    protected function read($cacheFile)
    {
        return $this->packer instanceof PhpPacker
            ? include $cacheFile
            : $this->unpack(file_get_contents($cacheFile));
    }
}
