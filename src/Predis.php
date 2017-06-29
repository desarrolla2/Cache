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
 * Predis
 */
class Predis extends AbstractAdapter
{
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
    public function del($key)
    {
        $cmd = $this->predis->createCommand('DEL');
        $cmd->setArguments([$key]);

        $this->predis->executeCommand($cmd);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->unPack($this->predis->get($key));
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
        $this->predis->set($key, $this->pack($value));
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $cmd = $this->predis->createCommand('EXPIRE');
        $cmd->setArguments([$key, $ttl]);
        $this->predis->executeCommand($cmd);
    }
}
