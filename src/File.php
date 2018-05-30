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
 * Cache file.
 * Data contains both value and ttl
 */
class File extends AbstractFile
{
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

        if (!file_exists($cacheFile)) {
            return false;
        }

        $contents = $this->read($cacheFile);

        if ($contents['ttl'] < self::time()) {
            return false;
        }

        return $contents['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $cacheFile = $this->getFileName($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $contents = $this->read($cacheFile);

        return $contents['ttl'] < self::time();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFileName($key);

        $packed = $this->pack(compact('value', 'ttl'));

        if (!is_string($packed)) {
            throw new UnexpectedValueException("Packer must create a string for the data to be cached to file");
        }

        return file_put_contents($cacheFile, $packed);
    }
}
