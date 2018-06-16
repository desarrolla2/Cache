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
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\NopPacker;
use Memcached as BaseMemcached;

/**
 * Memcached
 */
class Memcached extends AbstractCache
{
    /**
     * @var BaseMemcached
     */
    protected $server;

    /**
     * @param BaseMemcached|null $server
     */
    public function __construct(BaseMemcached $server = null)
    {
        if (!$server) {
            $server = new BaseMemcached();
            $server->addServer('localhost', 11211);
        }

        $this->server = $server;
    }


    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }

    /**
     * Set the key prefix
     *
     * @param string $prefix
     * @return void
     */
    protected function setPrefixOption(string $prefix): void
    {
        $this->server->setOption(Memcached::OPT_PREFIX_KEY, $prefix);
    }

    /**
     * Get the key prefix
     *
     * @return string
     */
    protected function getPrefixOption(): string
    {
        return $this->server->getOption(Memcached::OPT_PREFIX_KEY) ?? '';
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

        if (strlen($key) > 250) {
            throw new InvalidArgumentException("Key to long, max 250 characters");
        }
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
            $this->assertKey(is_int($key) ? (string)$key : $key);
            $packed[$key] = $this->pack($value);
        }

        return $packed;
    }


    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $this->assertKey($key);

        $data = $this->server->get($key);

        if ($this->server->getResultCode() !== BaseMemcached::RES_SUCCESS) {
            return $default;
        }

        return $this->unpack($data);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $this->assertKey($key);
        $this->server->get($key);

        $result = $this->server->getResultCode();

        return $result === BaseMemcached::RES_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $this->assertKey($key);

        $packed = $this->pack($value);
        $ttlTime = $this->ttlToMemcachedTime($ttl);

        if ($ttlTime === false) {
            return $this->delete($key);
        }

        $success = $this->server->set($key, $packed, $ttlTime);

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->server->delete($this->keyToId($key));

        $result = $this->server->getResultCode();

        return $result === BaseMemcached::RES_SUCCESS || $result === BaseMemcached::RES_NOTFOUND;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');
        $keysArr = is_array($keys) ? $keys : iterator_to_array($keys, false);
        array_walk($keysArr, [$this, 'assertKey']);

        $result = $this->server->getMulti($keysArr);

        if ($result === false) {
            return false;
        }

        $items = array_fill_keys($keysArr, $default);

        foreach ($result as $key => $value) {
            $items[$key] = $this->unpack($value);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $packed = $this->packValues($values);
        $ttlTime = $this->ttlToMemcachedTime($ttl);

        if ($ttlTime === false) {
            return $this->server->deleteMulti(array_keys($packed));
        }

        return $this->server->setMulti($packed, $ttlTime);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
       return $this->server->flush();
    }


    /**
     * Convert ttl to timestamp or seconds.
     *
     * @see http://php.net/manual/en/memcached.expiration.php
     *
     * @param null|int|DateInterval $ttl
     * @return int|null
     * @throws InvalidArgumentException
     */
    protected function ttlToMemcachedTime($ttl)
    {
        $seconds = $this->ttlToSeconds($ttl);

        if ($seconds <= 0) {
            return isset($seconds) ? false : 0;
        }

        /* 2592000 seconds = 30 days */
        return $seconds <= 2592000 ? $seconds : $this->ttlToTimestamp($ttl);
    }
}
