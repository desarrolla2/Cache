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

namespace Desarrolla2\Cache\Adapter;

use Predis\Client;

/**
 * Redis
 */
class Redis extends AbstractAdapter
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379,
            ]
        );
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }

    /**
     * Delete a value from the cache
     *
     * @param string $key
     */
    public function delete($key)
    {
        $cmd = $this->client->createCommand('DEL');
        $cmd->setArguments([$key]);

        $this->client->executeCommand($cmd);
    }

    /**
     * Retrieve the value corresponding to a provided key
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * Retrieve the if value corresponding to a provided key exist
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $cmd = $this->client->createCommand('EXISTS');
        $cmd->setArguments([$key]);

        return $this->client->executeCommand($cmd);
    }

    /**
     * * Add a value to the cache under a unique key
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     */
    public function set($key, $value, $ttl = null)
    {
        $this->client->set($key, $value);
        if ($ttl) {
            $cmd = $this->client->createCommand('EXPIRE');
            $cmd->setArguments([$key, $ttl]);
            $this->client->executeCommand($cmd);
        }
    }
}
