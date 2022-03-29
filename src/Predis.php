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
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use Predis\Client;
use Predis\Response\ServerException;
use Predis\Response\Status;
use Predis\Response\ErrorInterface;

/**
 * Predis cache adapter.
 *
 * Errors are silently ignored but ServerExceptions are **not** caught. To PSR-16 compliant disable the `exception`
 * option.
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
    public function __construct(Client $client)
    {
        $this->predis = $client;
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
     * Run a predis command.
     *
     * @param string $cmd
     * @param mixed ...$args
     * @return mixed|bool
     */
    protected function execCommand(string $cmd, ...$args)
    {
        $command = $this->predis->createCommand($cmd, $args);
        $response = $this->predis->executeCommand($command);

        if ($response instanceof ErrorInterface) {
            return false;
        }

        if ($response instanceof Status) {
            return $response->getPayload() === 'OK';
        }

        return $response;
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
            return $this->execCommand('MSET', $dictionary);
        }

        $transaction = $this->predis->transaction();

        foreach ($dictionary as $key => $value) {
            $transaction->set($key, $value, 'EX', $ttlSeconds);
        }

        try {
            $responses = $transaction->execute();
        } catch (ServerException $e) {
            return false;
        }

        $ok = array_reduce($responses, function($ok, $response) {
            return $ok && $response instanceof Status && $response->getPayload() === 'OK';
        }, true);

        return $ok;
    }


    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $id = $this->keyToId($key);
        $response = $this->execCommand('GET', $id);

        return !empty($response) ? $this->unpack($response) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);
        $ids = array_keys($idKeyPairs);

        $response = $this->execCommand('MGET', $ids);

        if ($response === false) {
            return false;
        }

        $items = [];
        $packedItems = array_combine(array_values($idKeyPairs), $response);

        foreach ($packedItems as $key => $packed) {
            $items[$key] = isset($packed) ? $this->unpack($packed) : $default;
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->execCommand('EXISTS', $this->keyToId($key));
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
            return $this->execCommand('DEL', [$id]);
        }

        return !isset($ttlSeconds)
            ? $this->execCommand('SET', $id, $packed)
            : $this->execCommand('SETEX', $id, $ttlSeconds, $packed);
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
            return $this->execCommand('DEL', array_keys($dictionary));
        }

        return $this->msetExpire($dictionary, $ttlSeconds);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $id = $this->keyToId($key);

        return $this->execCommand('DEL', [$id]) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $ids = array_keys($this->mapKeysToIds($keys));

        return empty($ids) || $this->execCommand('DEL', $ids) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->execCommand('FLUSHDB');
    }
}
