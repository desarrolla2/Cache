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
    protected $mysqli;

    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'Only support keys up to 255 bytes'
    ];

    public function setUp()
    {
        if (!class_exists('mysqli')) {
            return $this->markTestSkipped("mysqli extension not loaded");
        }

        try {
            $this->mysqli = new \mysqli(
                ini_get('mysqli.default_host'),
                ini_get('mysqli.default_user') ?: 'root'
            );
        } catch (\Exception $e) {
            return $this->markTestSkipped("skipping mysqli test; " . mysqli_connect_error());
        }

        $this->mysqli->query('CREATE DATABASE IF NOT EXISTS `' . CACHE_TESTS_MYSQLI_DATABASE . '`');
        $this->mysqli->select_db(CACHE_TESTS_MYSQLI_DATABASE);

        $this->mysqli->query("CREATE TABLE IF NOT EXISTS `cache` "
            ."( `key` VARCHAR(255), `value` TEXT, `ttl` INT UNSIGNED, PRIMARY KEY (`key`) )");

        if ($this->mysqli->error) {
            $this->markTestSkipped($this->mysqli->error);
        }

        parent::setUp();
    }

    public function createSimpleCache()
    {
        return (new MysqliCache($this->mysqli))
            ->withOption('initialize', false);
    }

    public function tearDown()
    {
        $this->mysqli->query('DROP DATABASE IF EXISTS `' . CACHE_TESTS_MYSQLI_DATABASE . '`');
        $this->mysqli->close();
    }
}
