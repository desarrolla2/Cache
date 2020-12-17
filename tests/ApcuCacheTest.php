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
    public static function setUpBeforeClass(): void
    {
        // Required to check the TTL for new entries
        ini_set('apc.use_request_time', false);
    }

    public function createSimpleCache()
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
        
        return new ApcuCache();
    }
}
