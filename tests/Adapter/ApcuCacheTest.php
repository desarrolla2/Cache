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

use Desarrolla2\Cache\Apcu as ApcuCache;

/**
 * ApcuCacheTest
 */
class ApcuCacheTest extends AbstractCacheTest
{
    public function setUp()
    {
        if (!extension_loaded('apcu')) {
            $this->markTestSkipped(
                'The APCu extension is not available.'
            );
        }
        if (!ini_get('apc.enable_cli')) {
            $this->markTestSkipped(
                'You need to enable apc.enable_cli'
            );
        }
        
        $this->cache = new ApcuCache();
    }

    /**
     * @return array
     */
    public function dataProviderForOptionsException()
    {
        return [
            ['ttl', 0, '\Desarrolla2\Cache\Exception\CacheException'],
            ['file', 100, '\Desarrolla2\Cache\Exception\CacheException'],
        ];
    }
}
