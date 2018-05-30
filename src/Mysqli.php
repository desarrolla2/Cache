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

use mysqli as Server;

/**
 * Mysqli
 */
class Mysqli extends AbstractCache
{
    /**
     * @var Server
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
        $this->server = $server ?: new Server();
    }

    /**
     * Set the table name
     *
     * @param string $table
     */
    public function setTableOption($table)
    {
        $this->table = (string)$table;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTableOption()
    {
        return $this->table;
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->query('DELETE FROM {table} WHERE `key` = %s', $this->getKey($key)) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $row = $this->fetchRow(
            'SELECT `value` FROM {table} WHERE `key` = %s AND `ttl` >= %d LIMIT 1',
            $this->getKey($key),
            self::time()
        );

        return $row ? $this->unpack($row[0]) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');

        if (empty($keys)) {
            return [];
        }

        $cacheKeys = array_map([$this, 'getKey'], $keys);
        $items = array_fill_keys($cacheKeys, $default);

        $ret = $this->query(
            'SELECT `key`, `value` FROM {table} WHERE `key` IN `%s` AND `ttl` >= %d',
            $cacheKeys,
            self::time()
        );

        while ((list($key, $value) = $ret->fetch_assoc())) {
            $items[$key] = $this->unpack($value);
        }

        return $keys === $cacheKeys ? $items : array_merge($keys, array_values($items));
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $row = $this->fetchRow(
            'SELECT `key` FROM {table} WHERE `key` = %s AND `ttl` >= %d LIMIT 1',
            $this->getKey($key),
            self::time()
        );

        return !empty($row);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $res = $this->query(
            'REPLACE INTO {table} (`key`, `value`, `ttl`) VALUES (%s, %s, %d)',
            $this->getKey($key),
            $this->pack($value),
            self::time() + ($ttl ?: $this->ttl)
        );

        return $res !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        if (empty($values)) {
            return true;
        }

        $timeTtl = self::time() + ($ttl ?: $this->ttl);
        $query = 'REPLACE INTO {table} (`key`, `value`, `ttl`) VALUES';

        foreach ($values as $key => $value) {
            $query .= sprintf(
                '(%s, %s, %d),',
                $this->quote($this->getKey($key)),
                $this->quote($this->pack($value)),
                $this->quote($timeTtl)
            );
        }

        return $this->query(rtrim($query, ',')) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->query('TRUNCATE {table}');
    }

    /**
     * Fetch a row from a MySQL table
     *
     * @param string     $query
     * @param string[]   $params
     * @return array|false
     */
    protected function fetchRow($query, ...$params)
    {
        $res = $this->query($query, ...$params);

        if ($res === false) {
            return false;
        }

        return $res->fetch_row();
    }

    /**
     * Query the MySQL server
     *
     * @param string  $query
     * @param mixed[] $params
     * @return \mysqli_result|false;
     */
    protected function query($query, ...$params)
    {
        $saveParams = array_map([$this, 'quote'], $params);

        $baseSql = str_replace('{table}', $this->table, $query);
        $sql = vsprintf($baseSql, $saveParams);

        $ret = $this->server->query($sql);

        if ($ret === false) {
            trigger_error($this->server->error, E_USER_NOTICE);
        }

        return $ret;
    }

    /**
     * Quote a value to be used in an array
     *
     * @param mixed $value
     * @return mixed
     */
    protected function quote($value)
    {
        if (is_array($value)) {
            return join(', ', array_map([$this, 'quote'], $value));
        }

        return is_string($value)
            ? ('"' . $this->server->real_escape_string($value) . '"')
            : (is_float($value) ? (float)$value : (int)$value);
    }
}
