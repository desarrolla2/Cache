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

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\MongoCacheException;
use Mongo as MongoBase;

/**
 *
 * Description of Mongo
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 */
class Mongo extends AbstractAdapter
{

    protected $db;
    protected $mongo;

    /**
     *
     * @param  string             $server
     * @param  array              $options
     * @param  string             $database
     * @throws FileCacheException
     */
    public function __construct(
        $server = 'mongodb://localhost:27017',
        $options = array('connect' => true),
        $database = '__cache'
    ) {
        $this->mongo = new MongoBase();
        if (!$this->mongo) {
            throw new MongoCacheException(' Mongo connection fails ');
        }
        $this->db = $this->mongo->selectDB($database);
    }

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        $_key = $this->getKey($key);
        $this->db->items->remove(array('key' => $_key));
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        if ($data = $this->getData($key)) {
            return $data;
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        if ($this->getData($key)) {
            return true;
        }

        return false;
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
        $item = array(
            'key'   => $_key,
            'value' => $_value,
            'ttl'   => (int) $ttl + time(),
        );
        $this->delete($key);
        $this->db->items->insert($item);
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
                    throw new MongoCacheException('ttl cant be lower than 1');
                }
                $this->ttl = $value;
                break;
            default :
                throw new MongoCacheException('option not valid ' . $key);
        }

        return true;
    }

    /**
     * Get data value from file cache
     *
     * @param  string             $key
     * @return boolean
     * @throws FileCacheException
     */
    protected function getData($key, $delete = true)
    {
        $_key = $this->getKey($key);
        $data = $this->db->items->findOne(array('key' => $_key));
        if (count($data)) {
            $data = array_values($data);
            if (time() > $data[3]) {
                if ($delete) {
                    $this->delete($key);
                }

                return false;
            }

            return $this->unserialize($data[2]);
        }

        return false;
    }
}
