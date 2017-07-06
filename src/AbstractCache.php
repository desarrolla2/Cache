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

use Desarrolla2\Cache\CacheInterface;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use Desarrolla2\Cache\Exception\CacheException;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * AbstractAdapter
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * @var int
     */
    protected $ttl = 3600;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var PackerInterface 
     */
    protected $packer;
    
    
    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value)
    {
        $method = "set" . str_replace('-', '', $key) . "Option";
        
        if (empty($key) || !method_exists($this, $method)) {
            throw new InvalidArgumentException("unknown option '$key'");
        }
        
        $this->$method($value);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOption($key)
    {
        $method = "get" . str_replace('-', '', $key) . "Option";
        
        if (empty($key) || !method_exists($this, $method)) {
            throw new InvalidArgumentException("unknown option '$key'");
        }
        
        return $this->$method();
    }
    
    /**
     * Set the time to live (ttl)
     * 
     * @param int $value  Seconds
     * @throws InvalidArgumentException
     */
    protected function setTtlOption($value)
    {
        $ttl = (int)$value;
        if ($ttl < 1) {
            throw new InvalidArgumentException('ttl cant be lower than 1');
        }
        
        $this->ttl = $ttl;
    }
    
    /**
     * Get the time to live (ttl)
     * 
     * @return int
     */
    protected function getTtlOption()
    {
        return $this->ttl;
    }
    
    /**
     * Set the key prefix
     * 
     * @param string $value
     */
    protected function setPrefixOption($value)
    {
        $this->prefix = (string)$value;
    }

    /**
     * Get the key prefix
     * 
     * @return string
     */
    protected function getPrefixOption()
    {
        return $this->prefix;
    }
    
    
    /**
     * Set the packer
     * 
     * @param PackerInterface $packer
     */
    public function setPacker(PackerInterface $packer)
    {
        $this->packer = $packer;
    }
    
    /**
     * Get the packer
     * 
     * @return PackerInterface
     */
    protected function getPacker()
    {
        if (!isset($this->packer)) {
            $this->packer = new SerializePacker();
        }
        
        return $this->packer;
    }
    
    
    /**
     * Assert that the keys are traversable
     * 
     * @param iterable $subject
     * @param string   $msg
     * @throws InvalidArgumentException if subject are not iterable
     */
    protected function assertIterable($subject, $msg)
    {
        $iterable = function_exists('is_iterable')
            ? is_iterable($subject)
            : is_array($subject) || $subject instanceof Traversable;
        
        if (~$iterable) {
            throw new InvalidArgumentException($msg);
        }
    }
    
    
    /**
     * Get the key with prefix
     * 
     * @param string $key
     * @return string
     */
    protected function getKey($key)
    {
        return sprintf('%s%s', $this->prefix, $key);
    }


    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $this->assertTraverable($keys, 'keys not iterable');
        
        $success = true;
        
        foreach ($keys as $key) {
            $success &= $this->delete($key);
        }
        
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->assertTraverable($keys, 'keys not iterable');
        
        $result = [];
        
        foreach ($keys as $key) {
            $result[] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        throw new CacheException('not ready yet');
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->assertTraverable($values, 'values not iterable');
        
        $result = [];
        
        foreach ($values as $key => $value) {
            $result[] = $this->set($key, $value, $ttl);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        throw new CacheException('not ready yet');
    }
}
