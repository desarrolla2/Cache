<?php

/**
 * This file is part of the D2Cache proyect.
 *
 * Description of Apc
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : Apc.php , UTF-8
 * @date : Sep 4, 2012 , 1:00:27 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\ApcCacheException;

class Apc extends AbstractAdapter implements AdapterInterface
{

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            if (!\apc_delete($key)) {
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
            if (!$data = \apc_fetch($key)) {
                throw new ApcCacheException('Error fetching data with the key "' . $key . '" from the APC cache.');
            }

            return unserialize($data);
        }

        return null;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        if (function_exists("\apc_exists")) {
            return (boolean) \apc_exists($key);
        } else {
            \apc_fetch($key, $result);

            return (boolean) $result;
        }
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        if (is_null($ttl)) {
            $ttl = $this->ttl;
        }
        if (!\apc_store($key, serialize($value), $ttl)) {
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
