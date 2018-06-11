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

declare(strict_types=1);

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use MongoDB;
use MongoDB\BSON\UTCDatetime as BSONUTCDateTime;

/**
 * Mongo
 */
class Mongo extends AbstractCache
{
    /**
     * @var MongoDb\Collection
     */
    protected $collection;

    /**
     * Class constructor
     *
     * @param mixed $backend
     * @throws UnexpectedValueException
     * @throws MongoDB\Driver\Exception
     */
    public function __construct($backend = null)
    {
        if (!isset($backend)) {
            $backend = new MongoDB\Client();
        }

        if ($backend instanceof MongoDB\Client) {
            $backend = $backend->selectDatabase('cache');
        }

        if ($backend instanceof MongoDB\Collection) {
            $backend = $backend->selectCollection('items');
        }

        if (!$backend instanceof MongoDB\Database) {
            $type = (is_object($backend) ? get_class($backend) . ' ' : '') . gettype($backend);
            throw new UnexpectedValueException("Database should be a database (MongoDB\Database) or " .
                " collection (MongoDB\Collection) object, not a $type");
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
     * Class destructor
     */
    public function __destruct()
    {
        $this->predis->disconnect();
    }


    /**
     * Create the default packer for this cache implementation.
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
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
            'ttl' => $this->getTtlBSON($ttl ?: $this->ttl),
            'value' => $this->pack($value)
        ];

        $this->collection->save(['_id' => $cacheKey], $item, ['upsert' => true]);
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

        $bsonTtl = $this->getTtlBSON($ttl ?: $this->ttl);
        $items = [];

        foreach ($values as $key => $value) {
            $cacheKey = $this->getKey($key);

            $items[] = [
                'replaceOne' => [
                    'filter' => ['_id' => $cacheKey],
                    'replacement' => [
                        '_id' => $cacheKey,
                        'ttl' => $bsonTtl,
                        'value' => $this->pack($value)
                    ],
                    'upsert' => true
                ]
            ];
        }

        $this->collection->bulkWrite($items);
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
     * @param int|null $ttl
     * @return BSONUTCDatetime|null
     */
    protected function getTtlBSON(?int $ttl): ?BSONUTCDatetime
    {
        return isset($ttl) ? new BSONUTCDateTime($this->ttlToSeconds($ttl) * 1000) : null;
    }
}
