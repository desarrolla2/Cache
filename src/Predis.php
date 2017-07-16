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
    use PackTtlTrait;
    /**
     * @var Client
     */
    protected $predis;

    /**
     * @param Client $client
     *
     * @see predis documentation about how know your configuration
     * https://github.com/nrk/predis
     */
    public function __construct(Client $client = null)
    {
        if ($client) {
            $this->predis = $client;

            return;
        }
        $this->predis = new Client();
    }

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
        $cmd->setArguments([$key]);

        $this->predis->executeCommand($cmd);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {

        try {
            $data = $this->unpack($this->predis->get($key));
        } catch( UnexpectedValueException $e ){
            return $default;
        }  catch( CacheExpiredException $e ){
            return $default;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $cmd = $this->predis->createCommand('EXISTS');
        $cmd->setArguments([$key]);

        return $this->predis->executeCommand($cmd);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->predis->set($key, $this->pack($value, $ttl));
        $cmd = $this->predis->createCommand('EXPIRE');
        $cmd->setArguments([$key, $ttl]);
        $this->predis->executeCommand($cmd);
    }
}
