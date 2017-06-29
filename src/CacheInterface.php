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

namespace Desarrolla2\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

/**
 * CacheInterface
 */
interface CacheInterface extends PsrCacheInterface
{
    /**
     * Set option for cache
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value);
    
    /**
     * Get option for cache
     *
     * @param string $key
     */
    public function getOption($key);
}
