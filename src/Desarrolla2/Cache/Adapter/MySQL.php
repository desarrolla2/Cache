<?php

/**
 * This file is part of the Cache project.
 *
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\MySQLCacheException;
use \mysqli;

/**
 *
 * Description of MySQL
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 */
class MySQL extends AbstractAdapter implements AdapterInterface
{

    /**
     *
     * @var \mysqli
     */
    protected $mysql;

    public function __construct(
        $host = 'localhost',
        $user = 'root',
        $password = '',
        $database = 'cache',
        $port = '3306'
    ) {
        $this->mysql = new mysqli($host, $user, $password, $database, $port);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->mysql->close();
    }

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        $_key  = $this->getKey($key);
        $query = 'DELETE FROM cache WHERE hash = \'' . $_key . '\';';

        return $this->query($query);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $_key  = $this->getKey($key);
        $query = 'SELECT value FROM cache WHERE hash = \'' . $_key . '\'' .
            ' AND ttl >= ' . time() . ';';
        $res   = $this->fetch_object($query);
        if ($res) {
            return $this->unserialize($res->value);
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $_key  = $this->getKey($key);
        $query = 'SELECT COUNT(*) AS items FROM cache WHERE hash = ' .
            '\'' . $_key . '\' AND  ' .
            ' ttl >= ' . time() . ';';
        $res   = $this->fetch_object($query);
        if (!$res) {
            return false;
        }
        if ($res->items == '0') {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $this->delete($key);
        $_key   = $this->getKey($key);
        $_value = $this->escape(
            $this->serialize($value)
        );
        if (!($ttl)) {
            $ttl = $this->ttl;
        }
        $_ttl  = $ttl + time();
        $query = ' INSERT INTO cache (hash, value, ttl) VALUES (' .
            '\'' . $_key . '\', ' .
            '\'' . $_value . '\', ' .
            '\'' . $_ttl . '\' );';
        $this->query($query);
    }

    /**
     * {@inheritdoc }
     */
    protected function getKey($key)
    {
        $_key = parent::getKey($key);

        return $this->escape($_key);
    }

    /**
     *
     * @param string $query
     * @param string $mode
     * @return mixed
     */
    protected function fetch_object($query, $mode = MYSQLI_STORE_RESULT)
    {
        $res = $this->query($query, $mode);
        if ($res) {
            return $res->fetch_object();
        }

        return false;
    }

    /**
     *
     * @param string $query
     * @param string $mode
     * @return mixed
     */
    protected function query($query, $mode = MYSQLI_STORE_RESULT)
    {
        $res = $this->mysql->query($query, $mode);
        if ($res) {
            return $res;
        }

        return false;
    }

    /**
     *
     * @param string $key
     * @return string
     */
    private function escape($key)
    {
        return $this->mysql->real_escape_string($key);
    }
}
