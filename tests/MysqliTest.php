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

    public function setUp()
    {
        if (!class_exists('mysqli')) {
            return $this->markTestSkipped("mysqli extension not loaded");
        }

        $this->mysqli = new \mysqli(
            $this->config['mysql']['host'],
            $this->config['mysql']['user'],
            $this->config['mysql']['password'],
            null,
            $this->config['mysql']['port']
        );

        if ($this->mysqli->errno) {
            return $this->markTestSkipped($this->mysqli->error);
        }

        $this->mysqli->query('CREATE DATABASE IF NOT EXISTS `' . $this->config['mysql']['database'] . '`;');
        $this->mysqli->select_db($this->config['mysql']['database']);

        $this->mysqli->query('CREATE TEMPORARY TABLE IF NOT EXISTS `cache`( `key` VARCHAR(255), `value` TEXT, `ttl` INT UNSIGNED )');

        parent::setUp();
    }

    public function createSimpleCache()
    {
        return new MysqliCache($this->mysqli);
    }

    public function tearDown()
    {
        $this->mysqli->query('DROP DATABASE IF EXISTS `'.$this->config['mysql']['database'].'`;');
    }
}
