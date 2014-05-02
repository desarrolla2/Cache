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

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Adapter\AdapterInterface;

/**
 *
 * Description of AbstractAdapter
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 */
abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * @var int
     */
    protected $ttl          = 3600;
    
    /**
     * @var string
     */
    protected $prefix       = '';
    
    /**
     * @var bool
     */
    protected $serialize    = true;

    /**
     * {@inheritdoc }
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc }
     */
    public function setOption($key, $value = null)
    {
        if (is_null($value))
        {
            /* allow for a full array of options */
            foreach ($key as $k => $v) {
                $this->setOption($k, $v);
            }
        }
        
        switch ($key)
        {
            case 'ttl':
                $value = (int)$value;
                if ($value < 1) {
                    throw new CacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            case 'prefix':
                $this->prefix = (string) $value;
                break;
            case 'serialize':
                $this->serialize = (bool) $value;
                break;
            default:
                throw new CacheException('option not valid ' . $key);
        }

        return true;
    }

    /**
     * {@inheritdoc }
     */
    public function clearCache()
    {
        throw new Exception('not ready yet');
    }

    /**
     * {@inheritdoc }
     */
    public function dropCache()
    {
        throw new Exception('not ready yet');
    }

    /**
     *
     * @param  string $key
     * @return string
     */
    protected function getKey($key)
    {
        //return md5($key);
        return $key;
    }
    
    /**
     * Builds the key according to the prefix and other options
     * 
     * @param string key
     * @return string
     */
    protected function buildKey($key)
    {
        return $this->prefix . $key;
    }
    
    
    /**
     * Packages the data to be stored by the internal caching driver
     * according to the options on the adapter.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function packData($data)
    {
        if ($this->serialize) {
            return serialize($data);
        }
        return $data;
    }
    
    /**
     * Unpackages the data retrieved by the internal caching driver
     * according to the options on the adapter. This will be the inverse
     * of packData IF the options are set correctly.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function unpackData($data)
    {
        if ($this->serialize) {
            return unserialize($data);
        }
        return $data;
    }

    protected function serialize($value)
    {
        return serialize($value);
    }

    protected function unserialize($value)
    {
        return unserialize($value);
    }
}
