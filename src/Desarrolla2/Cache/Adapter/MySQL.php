<?php

/**
 * This file is part of the Cache proyect.
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
 * @file : MySQL.php , UTF-8
 * @date : Oct 24, 2012 , 12:12:59 AM
 */
class MySQL extends AbstractAdapter implements AdapterInterface
{

    /**
     *
     * @var \mysqli 
     */
    protected $mysql;

    public function __construct()
    {
        $this->mysql = new mysqli("localhost", "root", "", "cache_dev");
    }

    public function __destruct()
    {
        $this->mysql->close();
    }

    public function delete($key)
    {
        $_key = $this->getKey($key);
        $query = 'DELETE FROM cache WHERE hash = \'' . $_key . '\' LIMIT 1; ';
        return $this->mysql->query($query, MYSQLI_ASYNC);
    }

    public function get($key)
    {
        $_key = $this->getKey($key);
        $query = 'SELECT COUNT * FROM cache WHERE hash = \'' . $_key . '\'' .
                ' AND ttl <= ' . time() . '';
        var_dump($query, $this->mysql->query($query));
    }

    public function has($key)
    {
        $_key = $this->getKey($key);
        $query = 'SELECT COUNT(*) FROM cache WHERE hash = ' .
                '\'' . $_key . '\' AND  ' .
                ' ttl <= ' . time() . '';
        var_dump($query, $this->mysql->query($query));
    }

    public function set($key, $value, $ttl = null)
    {
        $_key = $this->getKey($key);
        $_value = $this->escape(
                $this->serialize($value)
        );
        $_ttl = $ttl + time();
        $query = 'DELETE FROM cache WHERE hash = \'' . $_key . '\' LIMIT 1; ' .
                ' INSERT INTO cache (hash, value, ttl) VALUES (' .
                '\'' . $_key . '\', ' .
                '\'' . $_value . '\', ' .
                '\'' . $_ttl . '\' );
        ';
        $this->mysql->query($query, MYSQLI_ASYNC);
    }

    protected function getKey($key)
    {
        $_key = parent::getKey($key);
        return $this->escape($_key);
    }

    private function escape($key)
    {
        return $this->mysql->real_escape_string($key);
    }

}
