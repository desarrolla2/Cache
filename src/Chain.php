<?php
/**
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

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Packer\NopPacker;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Use multiple cache adapters.
 */
class Chain extends AbstractCache
{
    /**
     * @var CacheInterface[]
     */
    protected $adapters;

    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }


    /**
     * Chain constructor.
     *
     * @param CacheInterface[] $adapters  Fastest to slowest
     */
    public function __construct(array $adapters)
    {
        foreach ($adapters as $adapter) {
            if (!$adapter instanceof CacheInterface) {
                throw new InvalidArgumentException("All adapters should be a cache implementation");
            }
        }

        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->set($key, $value, $ttl) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->setMultiple($values, $ttl) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        foreach ($this->adapters as $adapter) {
            $result = $adapter->get($key); // Not using $default as we want to get null if the adapter doesn't have it

            if (isset($result)) {
                return $result;
            }
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys are not iterable');

        $missing = [];
        $values = [];

        foreach ($keys as $key) {
            $this->assertKey($key);

            $missing[] = $key;
            $values[$key] = $default;
        }

        foreach ($this->adapters as $adapter) {
            if (empty($missing)) {
                break;
            }

            $found = [];
            foreach ($adapter->getMultiple($missing) as $key => $value) {
                if (isset($value)) {
                    $found[$key] = $value;
                }
            }

            $values = array_merge($values, $found);
            $missing = array_values(array_diff($missing, array_keys($found)));
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->delete($key) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->deleteMultiple($keys) && $success;
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->clear() && $success;
        }

        return $success;
    }
}
