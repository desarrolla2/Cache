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
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

declare(strict_types=1);

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use Redis as PhpRedis;

/**
 * PHP Redis cache adapter.
 *
 * Errors are silently ignored but ServerExceptions are **not** caught. To PSR-16 compliant disable the `exception`
 * option.
 */
class Redis extends AbstractCache
{
    /**
     * @var PhpRedis
     */
    protected $client;

    /**
     * Redis constructor.
     *
     * @param PhpRedis $client
     */
    public function __construct(PhpRedis $client)
    {
        $this->client = $client;
    }

    /**
     * Create the default packer for this cache implementation.
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Set multiple (mset) with expire
     *
     * @param array    $dictionary
     * @param int|null $ttlSeconds
     * @return bool
     */
    protected function msetExpire(array $dictionary, ?int $ttlSeconds): bool
    {
        if (empty($dictionary)) {
            return true;
        }

        if (!isset($ttlSeconds)) {
            return $this->client->mset($dictionary);
        }

        $transaction = $this->client->multi();

        foreach ($dictionary as $key => $value) {
            $transaction->set($key, $value, $ttlSeconds);
        }

        $responses = $transaction->exec();

        return array_reduce(
            $responses,
            function ($ok, $response) {
                return $ok && $response;
            },
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $response = $this->client->get($this->keyToId($key));

        return empty($response) ? $this->unpack($response) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);

        $response = $this->client->mget(array_keys($idKeyPairs));

        return array_map(
            function ($packed) use ($default) {
                return empty($packed) ? $this->unpack($packed) : $default;
            },
            array_combine(array_values($idKeyPairs), $response)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->client->exists($this->keyToId($key)) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $id = $this->keyToId($key);
        $packed = $this->pack($value);

        if (!is_string($packed)) {
            throw new UnexpectedValueException("Packer must create a string for the data");
        }

        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->client->del($id);
        }

        return !isset($ttlSeconds)
            ? $this->client->set($id, $packed)
            : $this->client->setex($id, $ttlSeconds, $packed);
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $dictionary = [];

        foreach ($values as $key => $value) {
            $id = $this->keyToId(is_int($key) ? (string)$key : $key);
            $packed = $this->pack($value);

            if (!is_string($packed)) {
                throw new UnexpectedValueException("Packer must create a string for the data");
            }

            $dictionary[$id] = $packed;
        }

        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->client->del(array_keys($dictionary));
        }

        return $this->msetExpire($dictionary, $ttlSeconds);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $id = $this->keyToId($key);

        return $this->client->del($id) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $ids = array_keys($this->mapKeysToIds($keys));

        return empty($ids) || $this->client->del($ids) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->client->flushDB();
    }
}
