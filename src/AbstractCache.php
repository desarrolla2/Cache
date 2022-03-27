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

use Desarrolla2\Cache\Option\PrefixTrait as PrefixOption;
use Desarrolla2\Cache\Option\TtlTrait as TtlOption;
use Desarrolla2\Cache\Packer\PackingTrait as Packing;
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use DateTimeImmutable;
use DateInterval;
use Traversable;

/**
 * AbstractAdapter
 */
abstract class AbstractCache implements CacheInterface
{
    use PrefixOption;
    use TtlOption;
    use Packing;

    /**
     * Make a clone of this object.
     *
     * @return static
     */
    protected function cloneSelf(): self
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withOption(string $key, $value): self
    {
        return $this->withOptions([$key => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options): self
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
     * Validate the key
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    protected function assertKey($key): void
    {
        if (!is_string($key)) {
            $type = (is_object($key) ? get_class($key) . ' ' : '') . gettype($key);
            throw new InvalidArgumentException("Expected key to be a string, not $type");
        }

        if ($key === '' || preg_match('~[{}()/\\\\@:]~', $key)) {
            throw new InvalidArgumentException("Invalid key '$key'");
        }
    }

    /**
     * Assert that the keys are an array or traversable
     * 
     * @param iterable $subject
     * @param string   $msg
     * @return void
     * @throws InvalidArgumentException if subject are not iterable
     */
    protected function assertIterable($subject, $msg): void
    {
        $iterable = function_exists('is_iterable')
            ? is_iterable($subject)
            : is_array($subject) || $subject instanceof Traversable;
        
        if (!$iterable) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Turn the key into a cache identifier
     *
     * @param string $key
     * @return string
     * @throws InvalidArgumentException
     */
    protected function keyToId($key): string
    {
        $this->assertKey($key);

        return sprintf('%s%s', $this->prefix, $key);
    }

    /**
     * Create a map with keys and ids
     *
     * @param iterable $keys
     * @return array
     * @throws InvalidArgumentException
     */
    protected function mapKeysToIds($keys): array
    {
        $this->assertIterable($keys, 'keys not iterable');

        $map = [];

        foreach ($keys as $key) {
            $id = $this->keyToId($key);
            $map[$id] = $key;
        }

        return $map;
    }


    /**
     * Pack all values and turn keys into ids
     *
     * @param iterable $values
     * @return array
     */
    protected function packValues(iterable $values): array
    {
        $packed = [];

        foreach ($values as $key => $value) {
            $id = $this->keyToId(is_int($key) ? (string)$key : $key);
            $packed[$id] = $this->pack($value);
        }

        return $packed;
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
     * Get the current time.
     *
     * @return int
     */
    protected function currentTimestamp(): int
    {
        return time();
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
        if (!isset($ttl)) {
            return $this->ttl;
        }

        if ($ttl instanceof DateInterval) {
            $reference = new DateTimeImmutable();
            $endTime = $reference->add($ttl);

            $ttl = $endTime->getTimestamp() - $reference->getTimestamp();
        }

        if (!is_int($ttl)) {
            $type = (is_object($ttl) ? get_class($ttl) . ' ' : '') . gettype($ttl);
            throw new InvalidArgumentException("ttl should be of type int or DateInterval, not $type");
        }

        return isset($this->ttl) ? min($ttl, $this->ttl) : $ttl;
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
            return isset($this->ttl) ? time() + $this->ttl : null;
        }

        if (is_int($ttl)) {
            return time() + (isset($this->ttl) ? min($ttl, $this->ttl) : $ttl);
        }

        if ($ttl instanceof DateInterval) {
            $timestamp = (new DateTimeImmutable())->add($ttl)->getTimestamp();

            return isset($this->ttl) ? min($timestamp, time() + $this->ttl) : $timestamp;
        }

        $type = (is_object($ttl) ? get_class($ttl) . ' ' : '') . gettype($ttl);
        throw new InvalidArgumentException("ttl should be of type int or DateInterval, not $type");
    }
}
