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

namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Adapter\Test\AbstractCacheTest;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\MySQL;

/**
 *
 * Description of MemoryTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file   : MemoryTest.php , UTF-8
 * @date   : Jun 30, 2013 , 4:22:06 PM
 */
class MySQLTest extends AbstractCacheTest
{

    /**
     * setup
     */
    public function setUp()
    {
        parent::setup();
        if (!extension_loaded('mysqlnd')) {
            $this->markTestSkipped(
                'The MySQLnd extension is not available.'
            );
        }
        $this->cache = new Cache(new MySQL(
            $this->config['mysql']['host'],
            $this->config['mysql']['user'],
            $this->config['mysql']['password'],
            $this->config['mysql']['database'],
            $this->config['mysql']['port']
        ));
    }

    /**
     * @return array
     */
    public function dataProviderForOptions()
    {
        return array(
            array('ttl', 100),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return array(
            array('ttl', 0, '\Desarrolla2\Cache\Exception\CacheException'),
            array('file', 100, '\Desarrolla2\Cache\Exception\CacheException'),
        );
    }
}