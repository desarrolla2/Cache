<?php

/**
 * This file is part of the Cache project.
 *
 * Description of Apc
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\ApcCacheException;

class Apc extends AbstractAdapter
{

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            $_key = $this->getKey($key);
            if (!\apc_delete($_key)) {
                throw new ApcCacheException('Error deleting data with the key "' . $key . '" from the APC cache.');
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        if ($this->has($key)) {
            $_key = $this->getKey($key);
            if (!$data = \apc_fetch($_key)) {
                throw new ApcCacheException('Error fetching data with the key "' . $key . '" from the APC cache.');
            }

            return $this->unserialize($data);
        }

        return null;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $_key = $this->getKey($key);
        if (function_exists("\apc_exists")) {
            return (boolean) \apc_exists($_key);
        } else {
            \apc_fetch($_key, $result);

            return (boolean) $result;
        }
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $_key   = $this->getKey($key);
        $_value = $this->serialize($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        if (!\apc_store($_key, $_value, $ttl)) {
            throw new ApcCacheException('Error saving data with the key "' . $key . '" to the APC cache.');
        }
    }

    /**
     * {@inheritdoc }
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new ApcCacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            default :
                throw new ApcCacheException('option not valid ' . $key);
        }

        return true;
    }
}
