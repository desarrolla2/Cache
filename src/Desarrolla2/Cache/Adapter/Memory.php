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

/**
 * 
 * Description of Memory
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : Memory.php , UTF-8
 * @date : Jun 30, 2013 , 4:00:56 PM
 */
class Memory extends AbstractAdapter implements AdapterInterface
{

    /**
     *
     * @var int 
     */
    protected $limit = 100;

    /**
     *
     * @var array
     */
    protected $cache = array();

    /**
     * {@inheritdoc }
     */
    public function delete($key)
    {
        unset($this->cache[$this->getKey($key)]);
    }

    /**
     * {@inheritdoc }
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return unserialize($this->cache[$this->getKey($key)]['value']);
        }
        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function has($key)
    {
        if (isset($this->cache[$this->getKey($key)])) {
            $data = $this->cache[$this->getKey($key)];
            if (time() < $data['ttl'] + $data['time']) {
                return true;
            } else {
                $this->delete($key);
            }
        }
        return false;
    }

    /**
     * {@inheritdoc }
     */
    public function set($key, $value, $ttl = null)
    {
        while (count($this->cache) >= $this->limit) {
            array_shift($this->cache);
        }

        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $this->cache[$this->getKey($key)] = array(
            'value' => serialize($value),
            'ttl' => $ttl,
            'time' => time(),
        );
    }

    /**
     * {@inheritdoc }
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'limit':
                $value = (int) $value;
                if ($value < 1) {
                    throw new MemoryCacheException('limit cant be lower than 1');
                }
                $this->limit = $value;
                return true;
        }
        return parent::setOption($key, $value);
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    protected function getKey($key)
    {
        return md5($key);
    }

}