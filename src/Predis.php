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
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cmd = $this->predis->createCommand('DEL');
        $cmd->setArguments([$this->getKey($key)]);

        $this->predis->executeCommand($cmd);
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
    public function has($key)
    {
        $cmd = $this->predis->createCommand('EXISTS');
        $cmd->setArguments([$this->getKey($key)]);

        return $this->predis->executeCommand($cmd);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheKey = $this->getKey($key);

        $set = $this->predis->set($cacheKey, $this->pack($value));

        if (!$set) {
            return false;
        }

        $cmd = $this->predis->createCommand('EXPIRE');
        $cmd->setArguments([$cacheKey, $ttl]);

        $this->predis->executeCommand($cmd);

        return true;
    }
}
