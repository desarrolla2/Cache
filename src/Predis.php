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

use Desarrolla2\Cache\AbstractCache;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use Predis\Client;

/**
 * Predis
 */
class Predis extends AbstractCache
{
    /**
     * @var Client
     */
    protected $predis;

    /**
     * Class constructor
     * @see predis documentation about how know your configuration https://github.com/nrk/predis
     *
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        if (!$client) {
            $client = new Client();
        }

        $this->predis = $client;
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->predis->disconnect();
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
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->predis->executeRaw(['DEL', $this->getKey($key)]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $packed = $this->predis->get($this->getKey($key));

        return $this->unpack($packed);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');

        $transaction = $this->predis->transaction();

        foreach ($keys as $key) {
            $transaction->get($key);
        }

        $responses = $transaction->execute();

        return array_map(function ($value) use ($default) {
            return is_string($value) ? $this->unpack($value) : $default;
        }, $responses);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->predis->executeRaw(['EXISTS', $this->getKey($key)]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheKey = $this->getKey($key);

        $set = $this->predis->set($cacheKey, $this->pack($value));

        if ($set && isset($ttl)) {
            $this->predis->executeRaw(['EXPIRE', $cacheKey, $this->ttlToSeconds($ttl)]);
        }

        return $set;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $transaction = $this->predis->transaction();
        $ttlSeconds = $this->ttlToSeconds($ttl);

        foreach ($values as $key => $value) {
            $cacheKey = $this->getKey($key);
            $transaction->set($cacheKey);

            if (isset($ttlSeconds)) {
                $transaction->executeRaw(['EXPIRE', $cacheKey, $ttlSeconds]);
            }
        }

        $responses = $transaction->execute();

        return count(array_filter($responses)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->predis->executeRaw(['FLUSHDB']);
    }
}
