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
 * @author Arnold Daniels <arnold@jasny.net>
 */

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\AbstractCache;
use Desarrolla2\Cache\Exception\CacheException;

/**
 * Abstract class for using files as cache.
 *
 * @package Desarrolla2\Cache
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
    protected $filePrefix = '';

    /**
     * @var string
     */
    protected $fileSuffix;


    /**
     * @param string $cacheDir
     * @throws CacheException
     */
    public function __construct(string $cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()) . '/cache';
        }

        $this->cacheDir = (string)$cacheDir;
        $this->createCacheDirectory($cacheDir);
    }

    /**
     * Set the file prefix
     *
     * @param string $filePrefix
     * @return void
     */
    protected function setFilePrefixOption(string $filePrefix): void
    {
        $this->filePrefix = $filePrefix;
    }

    /**
     * Get the file prefix
     *
     * @return string
     */
    protected function getFilePrefixOption(): string
    {
        return $this->filePrefix;
    }

    /**
     * Set the file extension
     *
     * @param string $fileSuffix
     * @return void
     */
    protected function setFileSuffixOption(string $fileSuffix): void
    {
        $this->fileSuffix = $fileSuffix;
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    protected function getFileSuffixOption(): string
    {
        return isset($this->fileSuffix) ? $this->fileSuffix : ('.' . $this->getPacker()->getType());
    }


    /**
     * Create the cache directory
     *
     * @param string $cacheFile
     * @return void
     * @throws CacheException
     */
    protected function createCacheDirectory(string $path): void
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
     * Read the cache file
     *
     * @param $cacheFile
     * @return string
     */
    protected function readFile($cacheFile): string
    {
        return file_get_contents($cacheFile);
    }

    /**
     * Read the first line of the cache file
     *
     * @param string $cacheFile
     * @return string
     */
    protected function readLine(string $cacheFile): string
    {
        $fp = fopen($cacheFile, 'r');
        $line = fgets($fp);
        fclose($fp);

        return $line;
    }

    /**
     * Create a cache file
     *
     * @param string $cacheFile
     * @param string $contents
     * @return bool
     */
    protected function writeFile(string $cacheFile, string $contents): bool
    {
        return (bool)file_put_contents($cacheFile, $contents);
    }

    /**
     * Delete a cache file
     *
     * @param string $file
     * @return bool
     */
    protected function deleteFile(string $file): bool
    {
        return !is_file($file) || unlink($file);
    }

    /**
     * Create a filename based on the key
     *
     * @param string|mixed $key
     * @return string
     */
    protected function getFileName($key): string
    {
        return $this->cacheDir.
            DIRECTORY_SEPARATOR.
            $this->getFilePrefixOption().
            $this->getKey($key).
            $this->getFileSuffixOption();
    }
}