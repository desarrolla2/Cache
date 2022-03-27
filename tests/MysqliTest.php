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
 */

namespace Desarrolla2\Test\Cache;

use Desarrolla2\Cache\Mysqli as MysqliCache;

/**
 * MysqliTest
 */
class MysqliTest extends AbstractCacheTest
{
    /**
     * @var \mysqli
     */
    protected static $mysqli;

    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'Only support keys up to 255 bytes'
    ];

    public static function setUpBeforeClass(): void
    {
        if (class_exists('mysqli')) {
            static::$mysqli = new \mysqli(
                ini_get('mysqli.default_host') ?: 'localhost',
                ini_get('mysqli.default_user') ?: 'root'
            );
        }
    }

    public function init(): void
    {
        if (!class_exists('mysqli')) {
            $this->markTestSkipped("mysqli extension not loaded");
        }

        try {
            static::$mysqli->query('CREATE DATABASE IF NOT EXISTS `' . CACHE_TESTS_MYSQLI_DATABASE . '`');
            static::$mysqli->select_db(CACHE_TESTS_MYSQLI_DATABASE);

            static::$mysqli->query("CREATE TABLE IF NOT EXISTS `cache` "
                ."( `key` VARCHAR(255), `value` BLOB, `ttl` INT UNSIGNED, PRIMARY KEY (`key`) )");
        } catch (\Exception $e) {
            $this->markTestSkipped("skipping mysqli test; " . $e->getMessage());
        }

        if (static::$mysqli->error) {
            $this->markTestSkipped(static::$mysqli->error);
        }
    }

    public function createSimpleCache()
    {
        $this->init();

        return (new MysqliCache(static::$mysqli))
            ->withOption('initialize', false);
    }

    public static function tearDownAfterClass(): void
    {
        static::$mysqli->query('DROP DATABASE IF EXISTS `' . CACHE_TESTS_MYSQLI_DATABASE . '`');
        static::$mysqli->close();
    }
}
