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
 * @author Arnold Daniels <arnold@jasny.net>
 */

namespace Desarrolla2\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\KeyMaker\KeyMakerInterface;

/**
 * CacheInterface
 */
interface CacheInterface extends PsrCacheInterface
{
    /**
     * Set option for cache
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function withOption(string $key, $value);

    /**
     * Set multiple options for cache
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options);

    /**
     * Get option for cache
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key);

    /**
     * Set the packer
     *
     * @param PackerInterface $packer
     * @return static
     */
    public function withPacker(PackerInterface $packer);
}
