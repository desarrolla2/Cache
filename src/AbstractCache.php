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

use Desarrolla2\Cache\CacheInterface;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\KeyMaker\KeyMakerInterface;
use Desarrolla2\Cache\KeyMaker\PlainKeyMaker;
use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Exception\CacheExpiredException;
use DateTimeImmutable;
use DateInterval;

/**
 * AbstractAdapter
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * @var int|null
     */
    protected $ttl;

    /**
     * @var PackerInterface 
     */
    protected $packer;

    /**
     * @var KeyMakerInterface
     */
    protected $keyMaker;


    /**
     * Make a clone of this object.
     *
     * @return static
     */
    protected function cloneSelf()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withOption($key, $value)
    {
        return $this->withOptions([$key => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options)
    {
        $cache = $this->cloneSelf();

        foreach ($options as $key => $value) {
            $method = "set" . str_replace('-', '', $key) . "Option";

            if (empty($key) || !method_exists($cache, $method)) {
                throw new InvalidArgumentException("unknown option '$key'");
            }

            $cache->$method($value);
        }

        return $cache;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOption($key)
    {
        $method = "get" . str_replace('-', '', $key) . "Option";
        
        if (empty($key) || !method_exists($this, $method)) {
            throw new InvalidArgumentException("unknown option '$key'");
        }
        
        return $this->$method();
    }


    /**
     * Set the time to live (ttl)
     * 
     * @param int|null $value  Seconds
     * @throws InvalidArgumentException
     */
    protected function setTtlOption(?int $value)
    {
        if (isset($value) && $value < 1) {
            throw new InvalidArgumentException('ttl cant be lower than 1');
        }
        
        $this->ttl = $value;
    }
    
    /**
     * Get the time to live (ttl)
     * 
     * @return ?int
     */
    protected function getTtlOption(): ?int
    {
        return $this->ttl;
    }


    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    abstract protected static function createDefaultPacker(): PackerInterface;

    /**
     * Set a packer to pack (serialialize) and unpack (unserialize) the data.
     *
     * @param PackerInterface $packer
     * @return static
     */
    public function withPacker(PackerInterface $packer)
    {
        $cache = $this->cloneSelf();
        $cache->packer = $packer;

        return $cache;
    }
    
    /**
     * Get the packer
     * 
     * @return PackerInterface
     */
    protected function getPacker(): PackerInterface
    {
        if (!isset($this->packer)) {
            $this->packer = static::createDefaultPacker();
        }
        
        return $this->packer;
    }

    /**
     * Pack the value
     *
     * @param mixed $value
     * @return string|mixed
     */
    protected function pack($value)
    {
        return $this->getPacker()->pack($value);
    }

    /**
     * Unpack the data to retrieve the value
     *
     * @param string|mixed $packed
     * @return mixed
     * @throws UnexpectedValueException
     */
    protected function unpack($packed)
    {
        return $this->getPacker()->unpack($packed);
    }


    /**
     * {@inheritdoc}
     */
    public function withKeyMaker(KeyMakerInterface $keyMaker)
    {
        $cache = clone $this;
        $cache->keyMaker = $keyMaker;

        return $cache;
    }

    /**
     * Get the key maker
     *
     * @return KeyMakerInterface
     */
    protected function getKeyMaker()
    {
        if (!isset($this->keyMaker)) {
            $this->keyMaker = new PlainKeyMaker();
        }

        return $this->keyMaker;
    }

    /**
     * Get the key with prefix
     *
     * @param mixed $key
     * @return string
     */
    protected function getKey($key): string
    {
        return $this->getKeyMaker()->make($key);
    }


    /**
     * Assert that the keys are an array or traversable
     * 
     * @param iterable $subject
     * @param string   $msg
     * @throws InvalidArgumentException if subject are not iterable
     */
    protected function assertIterable($subject, $msg)
    {
        $iterable = function_exists('is_iterable')
            ? is_iterable($subject)
            : is_array($subject) || $subject instanceof Traversable;
        
        if (!$iterable) {
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        throw new CacheException('not ready yet');
    }


    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');
        
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $success = true;
        
        foreach ($values as $key => $value) {
            $success = $this->set(is_int($key) ? (string)$key : $key, $value, $ttl) && $success;
        }
        
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $this->assertIterable($keys, 'keys not iterable');

        $success = true;

        foreach ($keys as $key) {
            $success = $this->delete($key) && $success;
        }

        return $success;
    }


    /**
     * Convert TTL to seconds from now
     *
     * @param null|int|DateInterval $ttl
     * @return int|null
     * @throws InvalidArgumentException
     */
    protected function ttlToSeconds($ttl): ?int
    {
        if (!isset($ttl) || is_int($ttl)) {
            return $ttl;
        }

        if ($ttl instanceof DateInterval) {
            $reference = new DateTimeImmutable();
            $endTime = $reference->add($ttl);

            return $endTime->getTimestamp() - $reference->getTimestamp();
        }

        $type = (is_object($ttl) ? get_class($ttl) . ' ' : '') . gettype($ttl);
        throw new InvalidArgumentException("ttl should be of type int or DateInterval, not $type");
    }

    /**
     * Convert TTL to epoch timestamp
     *
     * @param null|int|DateInterval $ttl
     * @return int|null
     * @throws InvalidArgumentException
     */
    protected function ttlToTimestamp($ttl): ?int
    {
        if (!isset($ttl)) {
            return null;
        }

        if (is_int($ttl)) {
            return time() + $ttl;
        }

        if ($ttl instanceof DateInterval) {
            return (new DateTimeImmutable())->add($ttl)->getTimestamp();
        }

        $type = (is_object($ttl) ? get_class($ttl) . ' ' : '') . gettype($ttl);
        throw new InvalidArgumentException("ttl should be of type int or DateInterval, not $type");
    }
}
