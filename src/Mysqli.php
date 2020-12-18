<?php
/**
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

use Desarrolla2\Cache\Option\InitializeTrait;
use mysqli as Server;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;

/**
 * Mysqli cache adapter.
 *
 * Errors are silently ignored but exceptions are **not** caught. Beware when using `mysqli_report()` to throw a
 * `mysqli_sql_exception` on error.
 */
class Mysqli extends AbstractCache
{
    use InitializeTrait;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $table  = 'cache';


    /**
     * Class constructor
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }


    /**
     * Initialize table.
     * Automatically delete old cache.
     */
    protected function initialize(): void
    {
        if ($this->initialized !== false) {
            return;
        }

        $this->query(
            "CREATE TABLE IF NOT EXISTS `{table}` "
            . "( `key` VARCHAR(255), `value` BLOB, `ttl` INT UNSIGNED, PRIMARY KEY (`key`) )"
        );

        $this->query(
            "CREATE EVENT IF NOT EXISTS `apply_ttl_{$this->table}` ON SCHEDULE EVERY 1 HOUR DO BEGIN"
            . " DELETE FROM {table} WHERE `ttl` < NOW();"
            . " END"
        );

        $this->initialized = true;
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
     * Set the table name
     *
     * @param string $table
     */
    public function setTableOption(string $table)
    {
        $this->table = $table;
        $this->requireInitialization();
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTableOption(): string
    {
        return $this->table;
    }


    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $this->initialize();

        $result = $this->query(
            'SELECT `value` FROM {table} WHERE `key` = ? AND (`ttl` > ? OR `ttl` IS NULL) LIMIT 1',
            'si',
            $this->keyToId($key),
            $this->currentTimestamp()
        );

        $row = $result !== false ? $result->fetch_row() : null;

        return $row ? $this->unpack($row[0]) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);

        if (empty($idKeyPairs)) {
            return [];
        }

        $this->initialize();

        $values = array_fill_keys(array_values($idKeyPairs), $default);

        $placeholders = rtrim(str_repeat('?, ', count($idKeyPairs)), ', ');
        $paramTypes = str_repeat('s', count($idKeyPairs)) . 'i';
        $params = array_keys($idKeyPairs);
        $params[] = $this->currentTimestamp();

        $result = $this->query(
            "SELECT `key`, `value` FROM {table} WHERE `key` IN ($placeholders) AND (`ttl` > ? OR `ttl` IS NULL)",
            $paramTypes,
            ...$params
        );

        while (([$id, $value] = $result->fetch_row())) {
            $key = $idKeyPairs[$id];
            $values[$key] = $this->unpack($value);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $this->initialize();

        $result = $this->query(
            'SELECT COUNT(`key`) FROM {table} WHERE `key` = ? AND (`ttl` > ? OR `ttl` IS NULL) LIMIT 1',
            'si',
            $this->keyToId($key),
            $this->currentTimestamp()
        );

        [$count] = $result ? $result->fetch_row() : [null];

        return isset($count) && $count > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $this->initialize();

        $result = $this->query(
            'REPLACE INTO {table} (`key`, `value`, `ttl`) VALUES (?, ?, ?)',
            'ssi',
            $this->keyToId($key),
            $this->pack($value),
            $this->ttlToTimestamp($ttl)
        );

        return $result !== false;
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

        $this->initialize();

        $count = 0;
        $params = [];
        $timeTtl = $this->ttlToTimestamp($ttl);

        foreach ($values as $key => $value) {
            $count++;
            $params[] = $this->keyToId(is_int($key) ? (string)$key : $key);
            $params[] = $this->pack($value);
            $params[] = $timeTtl;
        }

        $query = 'REPLACE INTO {table} (`key`, `value`, `ttl`) VALUES '
            . rtrim(str_repeat('(?, ?, ?), ', $count), ', ');

        return (bool)$this->query($query, str_repeat('ssi', $count), ...$params);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->initialize();

        return (bool)$this->query(
            'DELETE FROM {table} WHERE `key` = ?',
            's',
            $this->keyToId($key)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);

        if (empty($idKeyPairs)) {
            return true;
        }

        $this->initialize();

        $placeholders = rtrim(str_repeat('?, ', count($idKeyPairs)), ', ');
        $paramTypes = str_repeat('s', count($idKeyPairs));

        return (bool)$this->query(
            "DELETE FROM {table} WHERE `key` IN ($placeholders)",
            $paramTypes,
            ...array_keys($idKeyPairs)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->initialize();
        return (bool)$this->query('TRUNCATE {table}');
    }


    /**
     * Query the MySQL server
     *
     * @param string  $query
     * @param string  $types
     * @param mixed[] $params
     * @return \mysqli_result|bool
     */
    protected function query($query, $types = '', ...$params)
    {
        $sql = str_replace('{table}', $this->table, $query);

        if ($params === []) {
            $ret = $this->server->query($sql);
        } else {
            $statement = $this->server->prepare($sql);

            if ($statement !== false) {
                $statement->bind_param($types, ...$params);

                $ret = $statement->execute();
                $ret = $ret ? ($statement->get_result() ?: true) : false;
            } else {
                $ret = false;
            }
        }

        if ($this->server->error) {
            trigger_error($this->server->error . " $sql", E_USER_NOTICE);
        }

        return $ret;
    }
}
