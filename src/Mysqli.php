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
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use mysqli as Server;

/**
 * Mysqli
 */
class Mysqli extends AbstractCache
{
    use PackTtlTrait {
        pack as protected traitpack;
    }
    /**
     * @var \mysqli
     */
    protected $server;

    /**
     * @var string
     */
    protected $table  = 'cache';

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

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->query(
            sprintf(
                'DELETE FROM %s WHERE k = "%s" OR t < %d',
                $this->table,
                $this->getKey($key),
                time()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $res = $this->fetchObject(
            sprintf(
                'SELECT v FROM %s WHERE k = "%s" AND t >= %d LIMIT 1;',
                $this->table,
                $this->getKey($key),
                time()
            )
        );
        if ($res) {
            return $this->unPack($res->v);
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $res = $this->fetchObject(
            sprintf(
                'SELECT COUNT(*) AS n FROM %s WHERE k = "%s" AND t >= %d;',
                $this->table,
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
        $this->delete($key);
        if (!($ttl)) {
            $ttl = $this->ttl;
        }
        $tTtl = (int) $ttl + time();
        $this->query(
            sprintf(
                'INSERT INTO %s (k, v, t) VALUES ("%s", "%s", %d)',
                $this->table,
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
        return $this->escape($this->traitpack($value, false));
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
