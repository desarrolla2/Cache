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

use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Apcu
 */
class Apcu extends AbstractCache
{
    use PackTtlTrait;

    /**
     * Set the `pack-ttl` setting; Include TTL in the packed data.
     * 
     * @param boolean $value
     */
    protected function setPackTtlOption($value)
    {
        $this->packTtl = (boolean)$value;
    }

    /**
     * Get the `pack-ttl` setting
     * 
     * @return boolean
     */
    protected function getPackTtlOption()
    {
        return $this->packTtl;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        apcu_delete($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->getValueFromCache($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return apcu_exists($key) && (!$this->packTtl || $this->getValueFromCache($key) !== null);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }

        $data = $this->pack($value, $ttl);
        
        if (!is_string($data)) {
            throw new InvalidArgumentException(
                sprintf('Error saving data with the key "%s" to the apcu cache; data must be packed as string', $key)
            );
        }
        
        $success = apcu_store($this->getKey($key), $data, $ttl);
        
        if (!$success) {
            throw new CacheException(sprintf('Error saving data with the key "%s" to the apcu cache.', $key));
        }
    }
    
    /**
     * Get the value from cache
     * 
     * @param string $key
     * @return mixed|null
     */
    protected function getValueFromCache($key, $default = null)
    {
        $packed = apcu_fetch($this->getKey($key), $success);
        
        if (!$success) {
            return $default;
        }
        
        try {
            $value = $this->unpack($packed);
        } catch (UnexpectedValueException $e) {
            $this->delete($key);
            return $default;
        }
        
        return $data['value'];
    }
}
