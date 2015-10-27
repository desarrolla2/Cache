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

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\CacheException;

/**
 * AbstractAdapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var int
     */
    protected $ttl = 3600;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new CacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            case 'prefix':
                $this->prefix = (string) $value;
                break;
            default:
                throw new CacheException('option not valid '.$key);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearCache()
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function dropCache()
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        throw new CacheException('not ready yet');
    }

    protected function getKey($key)
    {
        return sprintf('%s%s', $this->prefix, $key);
    }

    protected function pack($value)
    {
        return serialize($value);
    }

    protected function unPack($value)
    {
        return unserialize($value);
    }
}
