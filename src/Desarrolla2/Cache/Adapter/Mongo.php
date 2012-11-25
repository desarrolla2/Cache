<?php

/**
 * This file is part of the Cache proyect.
 * 
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\MongoCacheException;
use Mongo as MongoBase;

/**
 * 
 * Description of Mongo
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : Mongo.php , UTF-8
 * @date : Nov 12, 2012 , 1:12:12 AM
 */
class Mongo extends AbstractAdapter implements AdapterInterface
{

    protected $db;
    protected $mongo;

    /**
     * 
     * @param type $server
     * @param type $options
     * @param type $database
     * @throws FileCacheException
     */
    public function __construct($server = 'mongodb://localhost:27017', $options = array('connect' => true), $database = '__cache')
    {
        $this->mongo = new MongoBase();
        if (!$this->mongo) {
            throw new MongoCacheException(' mongo connection fails ');
        }
        $this->db = $this->mongo->selectDB($database);
    }

    /**
     * {@inheritdoc } 
     */
    public function delete($key)
    {
        $this->db->items->remove(array('key' => $key));
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
        $item = array(
            'key'   => $key,
            'value' => $value,
            'ttl'   => $ttl,
            'time'  => time(),
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
     * Get data value from file cache
     * 
     * @param type $key
     * @return boolean
     * @throws FileCacheException
     */
    protected function getData($key)
    {
        $data = $this->db->items->findOne(array('key' => $key));
        if (count($data)) {
            if ($data = array_values($data)) {
                if (time() > $data[4] + $data[3]) {
                    return false;
                }
                return $data[2];
            }
        }
        return false;
    }

}
