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

use mysqli as Server;

/**
 * Mysqli
 */
class Mysqli extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var \mysqli
     */
    protected $server;

    /**
     * @var string
     */
    protected $database = 'cache';

    /**
     * @param Server|null $server
     */
    public function __construct(Server $server = null)
    {
        if ($server) {
            $this->server = $server;

            return;
        }
        $this->server = new server();
    }

    public function __destruct()
    {
        $this->server->close();
    }

    /**
     * {@inheritdoc}
     */
    public function del($key)
    {
        $this->query(
            sprintf(
                'DELETE FROM %s WHERE k = "%s" OR t < %d',
                $this->database,
                $this->getKey($key),
                $this->database,
                time()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $res = $this->fetchObject(
            sprintf(
                'SELECT v FROM %s WHERE k = "%s" AND t >= %d LIMIT 1;',
                $this->database,
                $this->getKey($key),
                time()
            )
        );
        if ($res) {
            return $this->unPack($res->v);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $res = $this->fetchObject(
            sprintf(
                'SELECT COUNT(*) AS n FROM %s WHERE k = "%s" AND t >= %d;',
                $this->database,
                $this->getKey($key),
                time()
            )
        );
        if (!$res) {
            return false;
        }
        if ($res->n == '0') {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $this->del($key);
        if (!($ttl)) {
            $ttl = $this->ttl;
        }
        $tTtl = (int) $ttl + time();
        $this->query(
            sprintf(
                'INSERT INTO %s (k, v, t) VALUES ("%s", "%s", %d)',
                $this->database,
                $this->getKey($key),
                $this->pack($value),
                $tTtl
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getKey($key)
    {
        return $this->escape(parent::getKey($key));
    }

    protected function pack($value)
    {
        return $this->escape(parent::pack($value));
    }

    /**
     *
     * @param string     $query
     * @param int|string $mode
     *
     * @return mixed
     */
    protected function fetchObject($query, $mode = MYSQLI_STORE_RESULT)
    {
        $res = $this->query($query, $mode);
        if ($res) {
            return $res->fetch_object();
        }

        return false;
    }

    /**
     *
     * @param string     $query
     * @param int|string $mode
     *
     * @return mixed
     */
    protected function query($query, $mode = MYSQLI_STORE_RESULT)
    {
        $res = $this->server->query($query, $mode);
        if ($res) {
            return $res;
        }

        return false;
    }

    /**
     *
     * @param string $key
     *
     * @return string
     */
    private function escape($key)
    {
        return $this->server->real_escape_string($key);
    }
}
