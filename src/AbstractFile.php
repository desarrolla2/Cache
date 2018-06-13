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
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\File\BasicFilename;

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
     * @var callable
     */
    protected $filename;


    /**
     * @param string $cacheDir
     * @throws CacheException
     */
    public function __construct(string $cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'cache';
        }

        $this->cacheDir = (string)$cacheDir;
        $this->createCacheDirectory($cacheDir);
    }

    /**
     * Filename format or callable.
     * The filename format will be applied using sprintf, replacing `%s` with the key.
     *
     * @param string|callable $filename
     * @return void
     */
    protected function setFilenameOption($filename): void
    {
        if (is_string($filename)) {
            $filename = new BasicFilename($filename);
        }

        if (!is_callable($filename) || !is_object($filename)) {
            throw new \TypeError("Filename should be a string or callable");
        }

        $this->filename = $filename;
    }

    /**
     * Get the filename callable
     *
     * @return callable
     */
    protected function getFilenameOption(): callable
    {
        if (!isset($this->filename)) {
            $this->filename = new BasicFilename('%s.' . $this->getPacker()->getType());
        }

        return $this->filename;
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
    protected function getFilename($key): string
    {
        $cacheKey = $this->getKey($key);
        $generator = $this->getFilenameOption();

        return $this->cacheDir . DIRECTORY_SEPARATOR . $generator($key);
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cacheFile = $this->getFilename($key);

        return $this->deleteFile($cacheFile);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $generator = $this->getFilenameOption();
        $pattern = $this->cacheDir . DIRECTORY_SEPARATOR . $generator('');

        foreach (glob($pattern) as $file) {
            $this->deleteFile($file);
        }

        return true;
    }
}
