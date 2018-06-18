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
use Desarrolla2\Cache\Option\FilenameTrait as FilenameOption;
use Webmozart\Glob\Iterator\GlobIterator;

/**
 * Abstract class for using files as cache.
 *
 * @package Desarrolla2\Cache
 */
abstract class AbstractFile extends AbstractCache
{
    use FilenameOption;

    /**
     * @var string
     */
    protected $cacheDir;


    /**
     * Class constructor
     *
     * @param string|null $cacheDir
     * @throws CacheException
     */
    public function __construct(?string $cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'cache';
        }

        $this->cacheDir = rtrim($cacheDir, '/');
    }

    /**
     * Validate the key
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    protected function assertKey($key): void
    {
        parent::assertKey($key);

        if (strpos($key, '*')) {
            throw new InvalidArgumentException("Key may not contain the character '*'");
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
        $dir = dirname($cacheFile);

        if ($dir !== $this->cacheDir && !is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

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
     * Recursive delete an empty directory.
     *
     * @return bool
     */
    protected function removeFiles()
    {
        $generator = $this->getFilenameOption();
        $pattern = $this->cacheDir . DIRECTORY_SEPARATOR . $generator('*');

        $objects = new GlobIterator($pattern);

        foreach ($objects as $object) {
            unlink($object);
        }
    }

    /**
     * Recursive delete an empty directory.
     *
     * @param string $dir
     * @return bool
     */
    protected function removeChildDirecotries(string $dir = null)
    {
        if (empty($dir)) {
            $dir = $this->cacheDir;
        }

        $success = true;
        $objects = new GlobIterator($dir);

        foreach ($objects as $object) {
            if (!is_dir("$dir/$object") && is_link("$dir/$object")) {
                $success = $this->recursiveRemove("$dir/$object") && rmdir($dir) && $success;
            }
        }

        return $success;
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
     * Delete cache directory.
     *
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->removeFiles();

        return $this->removeChildDirecotries();
    }
}
