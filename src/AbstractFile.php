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

use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Option\FilenameTrait as FilenameOption;

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
     * Get the contents of the cache file.
     *
     * @param string $cacheFile
     * @return string
     */
    protected function readFile(string $cacheFile): string
    {
        return file_get_contents($cacheFile);
    }

    /**
     * Read the first line of the cache file.
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
     * Remove all files from a directory.
     */
    protected function removeFiles(string $dir): bool
    {
        $success = true;

        $generator = $this->getFilenameOption();
        $objects = $this->streamSafeGlob($dir, $generator('*'));

        foreach ($objects as $object) {
            $success = $this->deleteFile($object) && $success;
        }

        return $success;
    }

    /**
     * Recursive delete an empty directory.
     *
     * @param string $dir
     */
    protected function removeRecursively(string $dir): bool
    {
        $success = $this->removeFiles($dir);

        $objects = $this->streamSafeGlob($dir, '*');

        foreach ($objects as $object) {
            if (!is_dir($object)) {
                continue;
            }

            if (is_link($object)) {
                unlink($object);
            } else {
                $success = $this->removeRecursively($object) && $success;
                rmdir($object);
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
        $this->removeRecursively($this->cacheDir);

        return true;
    }

    /**
     * Glob that is safe with streams (vfs for example)
     *
     * @param string $directory
     * @param string $filePattern
     * @return array
     */
    protected function streamSafeGlob(string $directory, string $filePattern): array
    {
        $filePattern = basename($filePattern);
        $files = scandir($directory);
        $found = [];

        foreach ($files as $filename) {
            if (in_array($filename, ['.', '..'])) {
                continue;
            }

            if (fnmatch($filePattern, $filename) || fnmatch($filePattern . '.ttl', $filename)) {
                $found[] = "{$directory}/{$filename}";
            }
        }

        return $found;
    }
}
