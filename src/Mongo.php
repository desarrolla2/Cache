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

use Desarrolla2\Cache\Exception\UnexpectedValueException;
use MongoDB;
use MongoClient;
use MongoCollection;
use MongoDate;
use MongoConnectionException;

/**
 * Mongo
 */
class Mongo extends AbstractCache
{
    /**
     * @var MongoCollection|MongoDb\Collection
     */
    protected $collection;

    /**
     * Class constructor
     *
     * @param mixed $backend
     * @throws UnexpectedValueException
     * @throws MongoConnectionException
     * @throws MongoDB\Driver\Exception
     */
    public function __construct($backend = null)
    {
        if (!isset($backend)) {
            $backend = class_exist('MongoClient') ? new MongoClient() : new MongoDB\Client();
        }

        if ($backend instanceof MongoClient || $backend instanceof MongoDB\Client) {
            $backend = $backend->selectDatabase('cache');
        }

        if ($backend instanceof MongoCollection || $backend instanceof MongoDB\Collection) {
            $backend = $backend->selectCollection('items');
        }

        if (!$backend instanceof MongoDB && !$backend instanceof MongoDB\Database) {
            $type = (is_object($backend) ? get_class($backend) . ' ' : '') . gettype($backend);
            throw new UnexpectedValueException("Database should be a database (MongoDB or MongoDB\Database) or " .
                " collection (MongoCollection or MongoDB\Collection) object, not a $type");
        }

        $this->collection = $backend;

        $this->initCollection();
    }

    /**
     * Initialize the DB collecition
     */
    protected function initCollection()
    {
        // TTL index
        $this->collection->createIndex(['ttl' => 1], ['expireAfterSeconds' => 0]);
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $cacheKey = $this->getKey($key);

        $this->collection->remove(['_id' => $cacheKey]);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key, $default = null)
    {
        $data = $this->collection->findOne(['_id' => $this->getKey($key)]);

        return isset($data) ? $this->unpack($data['value']) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');

        if (empty($keys)) {
            return [];
        }

        $cacheKeys = array_map([$this, 'getKey'], $keys);
        $items = array_fill_keys($cacheKeys, $default);

        $rows = $this->collection->find(['_id' => ['$in' => $cacheKeys]]);

        foreach ($rows as $row) {
            $items[$row['_id']] = $this->unpack($row['value']);
        }

        return $keys === $cacheKeys ? $items : array_merge($keys, array_values($items));
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        return $this->collection->count(['_id' => $this->getKey($key)]) > 0;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheKey = $this->getKey($key);

        $item = [
            '_id' => $cacheKey,
            'ttl' => $this->getTtl($ttl ?: $this->ttl),
            'value' => $this->pack($value),
        ];

        $this->collection->update(['_id' => $cacheKey], $item, ['upsert' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        if (empty($values)) {
            return true;
        }

        $filters = [];
        $items = [];

        foreach ($values as $key => $value) {
            $cacheKey = $this->getKey($key);

            $filters[] = ['_id' => $cacheKey];

            $items[] = array(
                '_id' => $cacheKey,
                'ttl' => $this->getTtl($ttl ?: $this->ttl),
                'value' => $this->pack($value),
            );
        }

        $this->collection->updateMany($filters, $values, ['upsert' => true]);

    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->collection->dropCollection();

        $this->initCollection();
    }

    /**
     * Get TTL as Date type BSON object
     *
     * @param  int  $ttl
     * @return MongoDate|MongoDB\BSON\UTCDatetime
     */
    protected function getTtl($ttl = 0)
    {
        return $this->collection instanceof MongoCollection ?
            new MongoDate(self::time() + (int)$ttl) :
            new MongoDB\BSON\UTCDatetime((self::time() + (int)$ttl) * 1000);
    }
}
