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
use Desarrolla2\Cache\Exception\CacheExpiredException;
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Packer\PhpPacker;

/**
 * FlatFile.
 */
abstract class AbstractFile extends AbstractCache
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
     * @param string $file
     * @return bool
     */
    protected function deleteFile($file)
    {
        return is_file($file) && unlink($file);
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

}
