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

declare(strict_types=1);

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;

/**
 * Cache file.
 */
class File extends AbstractFile
{
    /**
     * @var string  'embed', 'file', 'mtime'
     */
    protected $ttlStrategy = 'embed';

    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Set TTL strategy
     *
     * @param string $strategy
     */
    protected function setTtlStrategyOption($strategy)
    {
        if (!in_array($strategy, ['embed', 'file', 'mtime'])) {
            throw new InvalidArgumentException("Unknown strategy '$strategy', should be 'embed', 'file' or 'mtime'");
        }

        $this->ttlStrategy = $strategy;
    }

    /**
     * Get TTL strategy
     *
     * @return string
     */
    protected function getTtlStrategyOption(): string
    {
        return $this->ttlStrategy;
    }


    /**
     * Get the TTL using one of the strategies
     *
     * @param string $cacheFile
     * @return int
     */
    protected function getTtl(string $cacheFile)
    {
        switch ($this->ttlStrategy) {
            case 'embed':
                return (int)$this->readLine($cacheFile);
            case 'file':
                return file_exists("$cacheFile.ttl")
                    ? (int)file_get_contents("$cacheFile.ttl")
                    : PHP_INT_MAX;
            case 'mtime':
                return $this->getTtl($cacheFile) > 0 ? filemtime($cacheFile) + $this->ttl : PHP_INT_MAX;
        }

        throw new \InvalidArgumentException("Invalid TTL strategy '{$this->ttlStrategy}'");
    }

    /**
     * Set the TTL using one of the strategies
     *
     * @param int|null $expiration
     * @param string   $contents
     * @param string   $cacheFile
     * @return string  The (modified) contents
     */
    protected function setTtl($expiration, $contents, $cacheFile)
    {
        switch ($this->ttlStrategy) {
            case 'embed':
                $contents = ($expiration ?? PHP_INT_MAX) . "\n" . $contents;
                break;
            case 'file':
                if ($expiration !== null) {
                    file_put_contents("$cacheFile.ttl", $expiration);
                }
                break;
            case 'mtime':
                // nothing
                break;
        }

        return $contents;
    }


    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $cacheFile = $this->getFilename($key);
        $packed = $this->readFile($cacheFile);

        if ($this->ttlStrategy === 'embed') {
            $packed = substr($packed, strpos($packed, "\n") + 1);
        }
        
        return $this->unpack($packed);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $cacheFile = $this->getFilename($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $ttl = $this->getTtl($cacheFile);

        if ($ttl <= time()) {
            $this->deleteFile($cacheFile);
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFilename($key);
        $packed = $this->pack($value);

        if (!is_string($packed)) {
            throw new UnexpectedValueException("Packer must create a string for the data to be cached to file");
        }

        $contents = $this->setTtl($this->ttlToTimestamp($ttl), $packed, $cacheFile);

        return $this->writeFile($cacheFile, $contents);
    }
}
