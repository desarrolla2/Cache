<?php

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Exception\UnexpectedValueException;
use Desarrolla2\Cache\Exception\CacheExpiredException;

/**
 * Methods for including the TTL in the 
 */
trait PackTtlTrait
{
    /**
     * Pack value and ttl and save to a single cache file
     * @var boolean
     */
    protected $packTtl = true;
    
    
    /**
     * @return PackerInterface
     */
    abstract protected function getPacker();

    
    /**
     * Pack the value, optionally include the ttl
     * 
     * @param mixed $value
     * @param int   $ttl
     * @return string|mixed $data
     */
    protected function pack($value, $ttl)
    {
        $data = $this->packTtl ? ['value' => $value, 'ttl' => time() + $ttl] : $value;

        return $this->getPacker()->pack($data);
    }
    
    /**
     * Unpack the data to retreive the value and optionally the ttl
     * 
     * @param string|mixed $packed
     * @return mixed
     * @throws UnexpectedValueException
     */
    protected function unpack($packed)
    {
        $data = $this->getPacker()->unpack($packed);
        
        if (!$this->validateDataFromCache($data)) {
            throw new UnexpectedValueException("unexpected data from cache");
        }
        
        if ($this->packTtl && $this->ttlHasExpired($data['ttl'])) {
            throw new CacheExpiredException("ttl has expired");
        }
        
        return $this->packTtl ? $data['value'] : $data;
    }
    
    /**
     * Validate that the data from cache is as expected
     * 
     * @param array|mixed $data
     * @return boolean
     */
    protected function validateDataFromCache($data)
    {
        return !$this->packTtl ||
            (is_array($data) && array_key_exists('value', $data) && array_key_exists('ttl', $data));
    }

    /**
     * Check if TTL has expired
     * 
     * @param type $ttl
     * @return type
     */
    protected function ttlHasExpired($ttl)
    {
        return (time() > $ttl);
    }
}
