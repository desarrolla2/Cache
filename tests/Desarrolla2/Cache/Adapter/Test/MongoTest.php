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
        parent::setup();
        if (!class_exists('Mongo')) {
            $this->markTestSkipped(
                    'The Mongo extension is not available.'
            );
        }
        $this->cache = new Cache(new Mongo($this->config['mongo']['dns']));
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
