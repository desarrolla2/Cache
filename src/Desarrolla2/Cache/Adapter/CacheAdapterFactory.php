<?php

/**
 * This file is part of the D2Cache project.
 *
 * Factory for different cache adapters
 *
 * @author : Ingo Theiss <ingo.theiss@i-matrixx.de>
 * @file : CacheAdapterFactory.php , UTF-8
 * @date : Dec 26, 2012 , 18:55:43 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Exception\RuntimeException;

/**
 * Factory to create an concrete adapter implementaion
 * to be used by the Cache class.
 */
class CacheAdapterFactory
{
    /**
     * Create an concrete adapter
     *
     * @param array $config Array of configuration options
     *
     * @return CacheInterface
     * @throws InvalidArgumentException
     */
    public static function factory($config = array())
    {
        /**
         * Some sane default cache adapter
         *
         * The default is specified in the bundles Configuration class but for safety
         * also specified here.
         *
         * @see Desarrolla2\Bundle\RSSClientBundle\DependencyInjection\Configuration for default adapter
         */
        $cacheAdapter = new NotCache();

        /**
         * Arguments for the adapter class constructor.
         * Not required or used at the moment.
         */
        $args = array();

        /** Namespace notation of a cache adapter */
        $adapter = $config['adapter'];
        /** The ttl option for the cache adapter */
        $ttl     = intval($config['ttl']);

        // Validate that the options are valid
        if (!is_array($config)) {
            throw new InvalidArgumentException('$config must be an array');
        }

        if (!is_string($adapter)) {
            throw new InvalidArgumentException('Parameter adapter must be namespace notation of a valid cache adapter');
        }

        if (!is_int($ttl)) {
            throw new InvalidArgumentException('Parameter ttl must be an integer');
        }

        if (!class_exists($adapter)) {
            throw new InvalidArgumentException($adapter . ' is not a valid adapter class');
        }

        /** If we passed so far we can instantiate the concrete adapter class */
        $cacheAdapter = self::createAdapter($adapter, $args);

        /**
         * Set ttl option
         *
         * The ttl is either the default 3600 or the value specified in the configuration.
         *
         * @see Desarrolla2\Bundle\RSSClientBundle\DependencyInjection\Configuration for defaults
         */
        $cacheAdapter->setOption('ttl', $ttl);

        return $cacheAdapter;
    }

    /**
     * Create an adapter using an array of constructor arguments
     *
     * @param string $className Class name
     * @param array  $args      Arguments for the class constructor
     *
     * @return AdapterInterface
     * @throws RuntimeException
     */
    protected static function createAdapter($className, array $args = null)
    {
        try {
            if (!$args) {
                return new $className;
            } else {
                $c = new \ReflectionClass($className);

                return $c->newInstanceArgs($args);
            }
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
