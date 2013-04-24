<?php

/**
 * This file is part of the Cache project.
 *
 * Factory for different cache adapters
 *
 * @author : Ingo Theiss <ingo.theiss@i-matrixx.de>
 * @author Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : AdapterFactory.php , UTF-8
 * @date : Dec 26, 2012 , 18:55:43 AM
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\Exception\RuntimeException;
use Desarrolla2\Cache\Adapter\NotCache;

/**
 * Factory to create an concrete adapter implementaion
 * to be used by the Cache class.
 */
class AdapterFactory
{

    /**
     * 
     * @param string $adapterName
     * @param array $args
     */
    public static function get($adapterName = 'Desarrolla2\Cache\Adapter\NotCache', array $args = null)
    {
        if (!class_exists($adapterName)) {
            throw new InvalidArgumentException($adapterName . ' is not a valid adapter class');
        }
    }

    /**
     * Create an concrete adapter
     *
     * @param array $config Array of configuration options
     *
     * @return CacheInterface
     * @throws InvalidArgumentException
     */
    public static function factory($config = array(), $args = null)
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException('$config must be an array');
        }
        if (!isset($config['adapter'])) {
            throw new InvalidArgumentException('$config[\'adapter\'] must be set');
        }
        $adapter = $config['adapter'];
        if (!is_string($adapter)) {
            throw new InvalidArgumentException('Parameter adapter must be a valid cache adapter');
        }

        if (!isset($config['ttl'])) {
            throw new InvalidArgumentException('$config[\'ttl\'] must be set');
        }
        $ttl = (int) $config['ttl'];
        if (!$ttl) {
            throw new InvalidArgumentException('Parameter ttl must be an integer');
        }



        $cacheAdapter = self::createAdapter($adapter, $args);
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
            $c = new \ReflectionClass($className);

            return $c->newInstanceArgs($args);
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
