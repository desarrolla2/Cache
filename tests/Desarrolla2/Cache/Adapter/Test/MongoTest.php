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

namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Adapter\Test\AbstractCacheTest;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Mongo;

/**
 *
 * Description of MongoTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : MongoTest.php , UTF-8
 * @date : Nov 25, 2012 , 1:58:13 AM
 */
class MongoTest extends AbstractCacheTest
{

    /**
     * setup
     */
    public function setUp()
    {
        $server = 'mongodb://localhost:27017';
        $this->cache = new Cache(new Mongo($server));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('key1', 'value', 1, 0, 'value', true),
            array('key2', 'value', null, 0, 'value', true),
            array('key3', 'value', 1, 2, false, false),
        );
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
            array('ttl', 0, '\Desarrolla2\Cache\Exception\MongoCacheException'),
            array('file', 100, '\Desarrolla2\Cache\Exception\MongoCacheException'),
        );
    }

}
