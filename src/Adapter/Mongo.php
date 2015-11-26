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
 * Mongo
 */
class Mongo extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var MongoCollection|MongoDB\Collection
     */
    protected $collection;

    /**
     * @param MongoDB|MongoDB\Database|MongoCollection|MongoDB\Collection  $backend
     */
    public function __construct($backend = null)
    {
        if (!isset($backend)) {
            $client = class_exist('MongoCollection') ? new \MongoClient() : new \MongoDB\Client();
            $backend = $client->selectDatabase('cache');
        }
            
        if ($backend instanceof \MongoCollection || $backend instanceof \MongoDB\Collection) {
            $this->collection = $backend;
        } elseif ($backend instanceof \MongoDB || $backend instanceof \MongoDB\Database) {
            $this->collection = $backend->selectCollection('items');
        } else {
            $type = (is_object($database) ? get_class($database) . ' ' : '') . gettype($database);
            throw new CacheException("Database should be a database (MongoDB or MongoDB\Database) or " .          
                " collection (MongoCollection or MongoDB\Collection) object, not a $type");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function del($key)
    {
        $tKey = $this->getKey($key);
        $this->collection->remove(array('_id' => $tKey));
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        $tKey = $this->getKey($key);
        $tNow = $this->getTtl();
        $data = $this->collection->findOne(array('_id' => $tKey, 'ttl' => array('$gte' => $tNow)));
        if (isset($data)) {
            return $this->unPack($data['value']);
        }

        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        $tKey = $this->getKey($key);
        $tNow = $this->getTtl();        
        return $this->collection->count(array('_id' => $tKey, 'ttl' => array('$gte' => $tNow))) > 0;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $tKey = $this->getKey($key);
        $tValue = $this->pack($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $item = array(
            '_id' => $tKey,
            'value' => $tValue,
            'ttl' => $this->getTtl($ttl),
        );
        $this->collection->update(array('_id' => $tKey), $item, array('upsert' => true));
    }
    
    /**
     * Get TTL as Date type BSON object
     *
     * @param  int  $ttl
     * @return MongoDate|MongoDB\BSON\UTCDatetime
     */
    protected function getTtl($ttl = 0)
    {
        return $this->collection instanceof \MongoCollection ?
            new \MongoDate((int) $ttl + time()) :
            new \MongoDB\BSON\UTCDatetime(((int) $ttl + time() * 1000));
    }
}
