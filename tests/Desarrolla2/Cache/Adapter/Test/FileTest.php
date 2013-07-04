<?php

/**
 * This file is part of the perrosygatos proyect.
 *
 * Description of CacheTest
 *
 * @author : Daniel González Cerviño <daniel.gonzalez@ideup.com>
 * @file : CacheTest.php , UTF-8
 * @date : Sep 4, 2012 , 3:49:01 PM
 */

namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Adapter\Test\AbstractCacheTest;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

class FileTest extends AbstractCacheTest
{

    /**
     * setup
     */
    public function setUp()
    {
        $this->cache = new Cache(new File());
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return array(
            array('ttl', 0, '\Desarrolla2\Cache\Exception\FileCacheException'),
            array('file', 100, '\Desarrolla2\Cache\Exception\FileCacheException'),
        );
    }

}
